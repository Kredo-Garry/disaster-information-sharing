<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\PhivolcsVolcanoAlert;
use Carbon\Carbon;

class FetchPhivolcsVolcanoAlerts extends Command
{
    protected $signature = 'phivolcs:fetch-volcano-alerts
        {--limit=5 : Number of rows to store}
        {--insecure : Disable TLS peer verification}
        {--cafile= : CA bundle path for TLS verification}
        {--debug : Print verbose debug info}
        {--url= : Override default URL}';

    protected $description = 'Fetch PHIVOLCS volcano bulletin via raw TLS socket and store (multi rows).';

    public function handle(): int
    {
        $fetchedAt = now();
        $insecure = (bool)$this->option('insecure');
        $limit = max(1, min(20, (int)$this->option('limit')));

        $url = trim((string)$this->option('url')) ?: 'https://www.phivolcs.dost.gov.ph/volcano-bulletin/';

        $res = $this->fetchUrlFollowRedirects($url, 'text/html', $insecure);

        if (!$res['ok']) {
            $this->error('Failed to fetch volcano bulletin.');
            if ($this->option('debug')) {
                $this->line("stage: " . ($res['stage'] ?? ''));
                if (isset($res['status'])) $this->line("status: " . $res['status']);
                if (isset($res['errno'])) $this->line("errno: " . $res['errno']);
                if (isset($res['errstr'])) $this->line("errstr: " . $res['errstr']);
            }
            return self::FAILURE;
        }

        $finalUrl = $res['final_url'] ?? $url;
        $html = $res['body'] ?? '';
        $text = $this->toCleanText($html);

        $rows = $this->parseVolcanoListFromText($text, $limit);

        if (count($rows) === 0) {
            $this->saveDebugFiles($html, $finalUrl, $text, 'volcano_nomatch');
            $this->warn('No volcano rows matched. Debug saved.');
            return self::SUCCESS;
        }

        $saved = 0;

        foreach ($rows as $r) {
            // volcano_name は必須扱い（取れない場合も埋める）
            $volcanoName = $r['volcano_name'] ?: 'Unknown Volcano';

            $hash = sha1(
                $volcanoName . '|' .
                (($r['issued_at_raw'] ?? '') ?: '') . '|' .
                substr($r['summary_text'] ?? '', 0, 800)
            );

            // ✅ DB設計に合わせて volcano_name のみに統一（volcano カラムは使わない）
            PhivolcsVolcanoAlert::updateOrCreate(
                ['hash' => $hash],
                [
                    'volcano_name' => $volcanoName,
                    'alert_level'  => $r['alert_level'],
                    'issued_at'    => $r['issued_at'],
                    'summary_text' => $r['summary_text'],
                    'full_text'    => $r['full_text'],
                    'source_url'   => $finalUrl,
                    'fetched_at'   => $fetchedAt,
                ]
            );

            $saved++;
        }

        $this->saveDebugFiles($html, $finalUrl, $text, 'volcano_best');

        $this->info("Saved/updated {$saved} volcano item(s) from volcano-bulletin list.");
        $this->info("Source URL: {$finalUrl}");
        return self::SUCCESS;
    }

    /**
     * volcano-bulletin ページは「複数の火山カード」が並ぶ。
     * 例:
     *  Taal Volcano Summary of 24Hr Observation 09 July 2025 12:00 AM ...
     */
    private function parseVolcanoListFromText(string $text, int $limit): array
    {
        $rows = [];

        // 「X Volcano Summary of 24Hr Observation 09 July 2025 12:00 AM」形式を拾う
        $re = '/\b([A-Z][A-Za-z\-]+)\s+Volcano\s+Summary\s+of\s+24Hr\s+Observation\s+([0-9]{1,2}\s+[A-Za-z]+\s+[0-9]{4})\s+([0-9]{1,2}:[0-9]{2}\s+(?:AM|PM))/i';

        if (preg_match_all($re, $text, $m, PREG_OFFSET_CAPTURE)) {
            $count = min(count($m[0]), $limit);

            for ($i = 0; $i < $count; $i++) {
                $name = trim($m[1][$i][0]) . ' Volcano';
                $date = trim($m[2][$i][0]);
                $time = trim($m[3][$i][0]);
                $issuedRaw = "{$date} {$time}";
                $issuedAt = $this->parseIssuedAt($issuedRaw);

                // 各カードの周辺テキストを簡易抽出（offsetを使って前後を切る）
                $start = max(0, $m[0][$i][1] - 120);
                $end = min(strlen($text), $m[0][$i][1] + 600);
                $snippet = trim(substr($text, $start, $end - $start));
                $snippet = preg_replace('/\s+/', ' ', $snippet);

                $rows[] = [
                    'volcano_name'  => $name,
                    'alert_level'   => null, // 一覧ページでは Alert Level が出ないことが多い
                    'issued_at'     => $issuedAt,
                    'issued_at_raw' => $issuedRaw,
                    'summary_text'  => mb_substr($snippet, 0, 800),
                    'full_text'     => $text, // 一覧ページ全文（必要なら後で個別ページ取得に拡張）
                ];
            }
        }

        return $rows;
    }

    private function parseIssuedAt(string $raw): ?Carbon
    {
        $raw = trim(preg_replace('/\s+/', ' ', $raw));
        $tz = 'Asia/Manila';

        // "09 July 2025 12:00 AM"
        try { return Carbon::createFromFormat('d F Y h:i A', $raw, $tz); } catch (\Throwable $e) {}
        try { return Carbon::parse($raw, $tz); } catch (\Throwable $e) { return null; }
    }

    private function toCleanText(string $html): string
    {
        $cleanHtml = preg_replace('~<script\b[^>]*>.*?</script>~is', ' ', $html);
        $cleanHtml = preg_replace('~<style\b[^>]*>.*?</style>~is', ' ', $cleanHtml);
        $text = strip_tags($cleanHtml);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }

    private function saveDebugFiles(string $html, string $url, string $text, string $tag): void
    {
        $dir = 'phivolcs_debug';
        Storage::makeDirectory($dir);
        $stamp = now()->format('Ymd_His');

        Storage::put("{$dir}/{$tag}_{$stamp}.html", $html);
        Storage::put("{$dir}/{$tag}_{$stamp}.txt", $text);

        Log::info("PHIVOLCS volcano debug saved", ['tag' => $tag, 'url' => $url]);
    }

    // ===== raw socket helpers (chunked + gzip 対応) =====

    private function fetchUrlFollowRedirects(string $url, string $accept, bool $insecure): array
    {
        $current = $url;
        for ($i = 0; $i < 5; $i++) {
            [$host, $path] = $this->splitUrl($current);

            $res = $this->fetchHttpsRawOnce($host, $path, $accept, $insecure);
            if (!$res['ok']) {
                $res['final_url'] = $current;
                return $res;
            }

            $status = (int)($res['status'] ?? 0);
            $headers = $res['headers'] ?? [];

            if (in_array($status, [301,302,303,307,308], true) && isset($headers['location'])) {
                $current = $this->resolveRedirectUrl($current, $headers['location']);
                continue;
            }

            $res['final_url'] = $current;
            return $res;
        }

        return ['ok' => false, 'stage' => 'redirect_loop', 'final_url' => $current];
    }

    private function resolveRedirectUrl(string $baseUrl, string $location): string
    {
        $location = trim($location);
        if (str_starts_with($location, 'http://') || str_starts_with($location, 'https://')) return $location;
        if (str_starts_with($location, '//')) {
            $p = parse_url($baseUrl);
            $scheme = $p['scheme'] ?? 'https';
            return $scheme . ':' . $location;
        }

        $p = parse_url($baseUrl);
        $scheme = $p['scheme'] ?? 'https';
        $host = $p['host'] ?? '';
        $basePath = $p['path'] ?? '/';

        if (str_starts_with($location, '/')) return "{$scheme}://{$host}{$location}";
        $dir = rtrim(str_replace(basename($basePath), '', $basePath), '/');
        return "{$scheme}://{$host}{$dir}/{$location}";
    }

    private function fetchHttpsRawOnce(string $host, string $path, string $accept, bool $insecure): array
    {
        $remote = "tls://{$host}:443";

        $ssl = [
            'verify_peer' => !$insecure,
            'verify_peer_name' => !$insecure,
            'SNI_enabled' => true,
            'peer_name' => $host,
            'crypto_method' => STREAM_CRYPTO_METHOD_TLS_CLIENT,
        ];

        if (!$insecure) {
            $cafile = trim((string)$this->option('cafile'));
            if ($cafile !== '' && is_file($cafile)) $ssl['cafile'] = $cafile;
        }

        $context = stream_context_create(['ssl' => $ssl]);

        $fp = @stream_socket_client($remote, $errno, $errstr, 25, STREAM_CLIENT_CONNECT, $context);
        if (!$fp) {
            return ['ok' => false, 'stage' => 'connect', 'errno' => $errno, 'errstr' => $errstr, 'host' => $host];
        }

        stream_set_timeout($fp, 30);

        $req =
            "GET {$path} HTTP/1.1\r\n" .
            "Host: {$host}\r\n" .
            "User-Agent: DIShiP/1.0 (+raw-socket)\r\n" .
            "Accept: {$accept}, */*\r\n" .
            "Accept-Encoding: identity\r\n" .
            "Connection: close\r\n\r\n";

        fwrite($fp, $req);

        $raw = '';
        while (!feof($fp)) {
            $chunk = fread($fp, 8192);
            if ($chunk === false) break;
            $raw .= $chunk;
        }
        $meta = stream_get_meta_data($fp);
        fclose($fp);

        $pos = strpos($raw, "\r\n\r\n");
        if ($pos === false) return ['ok' => false, 'stage' => 'split', 'rawHead' => mb_substr($raw, 0, 500), 'meta' => $meta];

        $rawHeader = substr($raw, 0, $pos);
        $body = substr($raw, $pos + 4);

        $lines = preg_split("/\r\n/", $rawHeader);
        $statusLine = $lines[0] ?? '';
        if (!preg_match('~^HTTP/\d\.\d\s+(\d{3})~', $statusLine, $sm)) {
            return ['ok' => false, 'stage' => 'status_parse', 'statusLine' => $statusLine, 'meta' => $meta];
        }

        $status = (int)$sm[1];
        $headers = [];
        foreach (array_slice($lines, 1) as $line) {
            $p = strpos($line, ':');
            if ($p === false) continue;
            $k = strtolower(trim(substr($line, 0, $p)));
            $v = trim(substr($line, $p + 1));
            if ($k !== '') $headers[$k] = $v;
        }

        if (isset($headers['transfer-encoding']) && stripos($headers['transfer-encoding'], 'chunked') !== false) {
            $body = $this->decodeChunked($body);
        }

        if (isset($headers['content-encoding']) && stripos($headers['content-encoding'], 'gzip') !== false) {
            $decoded = @gzdecode($body);
            if ($decoded !== false) $body = $decoded;
        }

        if ($status < 200 || $status >= 400) {
            return ['ok' => false, 'stage' => 'non2xx', 'status' => $status, 'headers' => $headers, 'bodyHead' => mb_substr($body, 0, 250), 'meta' => $meta];
        }

        return ['ok' => true, 'status' => $status, 'headers' => $headers, 'body' => $body, 'meta' => $meta];
    }

    private function decodeChunked(string $body): string
    {
        $decoded = '';
        $offset = 0;
        $len = strlen($body);

        while ($offset < $len) {
            $rnPos = strpos($body, "\r\n", $offset);
            if ($rnPos === false) break;

            $line = trim(substr($body, $offset, $rnPos - $offset));
            if ($line === '') break;

            $chunkSize = hexdec(preg_replace('/;.*$/', '', $line));
            $offset = $rnPos + 2;

            if ($chunkSize === 0) break;

            $decoded .= substr($body, $offset, $chunkSize);
            $offset += $chunkSize + 2;
        }

        return $decoded;
    }

    private function splitUrl(string $url): array
    {
        $p = parse_url($url);
        $host = $p['host'] ?? '';
        $path = ($p['path'] ?? '/') . (isset($p['query']) ? ('?' . $p['query']) : '');
        return [$host, $path];
    }
}
