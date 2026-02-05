<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\PhivolcsTsunamiBulletin;
use Carbon\Carbon;

class FetchPhivolcsTsunami extends Command
{
    protected $signature = 'phivolcs:fetch-tsunami
        {--insecure : Disable TLS peer verification}
        {--cafile= : CA bundle path for TLS verification}
        {--debug : Print verbose debug info}
        {--url= : Force a specific URL (skip discovery)}';

    protected $description = 'Fetch PHIVOLCS tsunami bulletin via raw TLS socket (discovery) and store latest.';

    public function handle(): int
    {
        $fetchedAt = now();
        $insecure = (bool)$this->option('insecure');
        $forcedUrl = trim((string)$this->option('url'));

        $candidates = array_values(array_filter([
            $forcedUrl ?: null,

            // よくある候補
            'https://www.phivolcs.dost.gov.ph/tsunami-bulletin/',
            'https://www.phivolcs.dost.gov.ph/tsunami/',
            'https://www.phivolcs.dost.gov.ph/tsunami-advisory-and-warning/',
            'https://www.phivolcs.dost.gov.ph/tsunami-advisory/',
            'https://www.phivolcs.dost.gov.ph/tsunami-warning/',
            'https://tsunami.phivolcs.dost.gov.ph/',
        ]));

        $best = null;

        foreach ($candidates as $url) {
            $res = $this->fetchUrlFollowRedirects($url, 'text/html', $insecure);
            if (!$res['ok']) {
                if ($this->option('debug')) {
                    $this->line("Candidate failed: {$url}");
                    $this->line("stage: " . ($res['stage'] ?? ''));
                    if (isset($res['status'])) $this->line("status: " . $res['status']);
                }
                continue;
            }

            $html = $res['body'] ?? '';
            $text = $this->toCleanText($html);
            $finalUrl = $res['final_url'] ?? $url;

            // 「Introduction」系を拾った場合、ページ内から "Latest Tsunami Bulletin" っぽいリンクを探す
            $maybeBulletinUrl = $this->findLatestBulletinUrl($html, $finalUrl);
            if ($maybeBulletinUrl && $maybeBulletinUrl !== $finalUrl) {
                $res2 = $this->fetchUrlFollowRedirects($maybeBulletinUrl, 'text/html', $insecure);
                if ($res2['ok']) {
                    $html = $res2['body'] ?? '';
                    $text = $this->toCleanText($html);
                    $finalUrl = $res2['final_url'] ?? $maybeBulletinUrl;
                }
            }

            $score = $this->scoreTsunamiPage($text);

            if (!$best || $score > $best['score']) {
                $best = [
                    'score' => $score,
                    'url' => $finalUrl,
                    'html' => $html,
                    'text' => $text,
                ];
            }

            if ($score >= 4) break;
        }

        if (!$best) {
            $this->error('Failed to fetch any tsunami page candidates.');
            return self::FAILURE;
        }

        $parsed = $this->parseTsunamiFromText($best['text']);

        $hash = sha1(($parsed['issued_at_raw'] ?? '') . '|' . ($parsed['bulletin_no'] ?? '') . '|' . substr($best['text'], 0, 2000));

        PhivolcsTsunamiBulletin::updateOrCreate(
            ['hash' => $hash],
            [
                'bulletin_no' => $parsed['bulletin_no'],
                'status' => $parsed['status'],
                'issued_at' => $parsed['issued_at'],
                'summary_text' => $parsed['summary_text'],
                'full_text' => $best['text'],
                'source_url' => $best['url'],
                'fetched_at' => $fetchedAt,
            ]
        );

        $this->saveDebugFiles($best['html'], $best['url'], $best['text'], 'tsunami_best');

        $this->info('Saved/updated 1 tsunami bulletin (latest).');
        $this->info("Source URL: {$best['url']}");
        return self::SUCCESS;
    }

    private function scoreTsunamiPage(string $text): int
    {
        $lc = strtolower($text);
        $score = 0;

        foreach (['tsunami', 'advisory', 'warning', 'bulletin'] as $kw) {
            if (str_contains($lc, $kw)) $score += 1;
        }

        // 「紹介ページ」はスコア下げる（あなたのログの "Introduction to Tsunami" がこれ）
        if (str_contains($lc, 'introduction to tsunami')) $score -= 2;
        if (str_contains($lc, 'preparedness')) $score -= 1;

        return $score;
    }

    private function findLatestBulletinUrl(string $html, string $baseUrl): ?string
    {
        // 「Latest Tsunami Bulletin」などのリンクを探す（メニューにあることが多い）
        if (preg_match_all('~<a\b[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)</a>~is', $html, $m)) {
            foreach ($m[1] as $i => $href) {
                $label = strip_tags($m[2][$i] ?? '');
                $label = strtolower(preg_replace('/\s+/', ' ', trim($label)));

                if (str_contains($label, 'latest tsunami bulletin') || str_contains($label, 'tsunami bulletin')) {
                    return $this->resolveUrl($baseUrl, $href);
                }
            }
        }

        // hrefにそれっぽい文字があるケース
        if (preg_match('~href=["\']([^"\']*(tsunami).*?(bulletin)[^"\']*)["\']~i', $html, $m2)) {
            return $this->resolveUrl($baseUrl, $m2[1]);
        }

        return null;
    }

    private function resolveUrl(string $baseUrl, string $href): string
    {
        $href = trim($href);
        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) return $href;
        if (str_starts_with($href, '//')) {
            $p = parse_url($baseUrl);
            $scheme = $p['scheme'] ?? 'https';
            return $scheme . ':' . $href;
        }

        $p = parse_url($baseUrl);
        $scheme = $p['scheme'] ?? 'https';
        $host = $p['host'] ?? '';
        if (str_starts_with($href, '/')) return "{$scheme}://{$host}{$href}";

        $basePath = $p['path'] ?? '/';
        $dir = rtrim(str_replace(basename($basePath), '', $basePath), '/');
        return "{$scheme}://{$host}{$dir}/{$href}";
    }

    private function parseTsunamiFromText(string $text): array
    {
        $status = null;
        $bulletinNo = null;
        $issuedAt = null;
        $issuedAtRaw = null;

        $lc = strtolower($text);

        foreach ([
            'tsunami warning' => 'Tsunami Warning',
            'tsunami advisory' => 'Tsunami Advisory',
            'tsunami information' => 'Tsunami Information',
            'all clear' => 'All Clear',
            'no tsunami' => 'No Tsunami Threat',
        ] as $needle => $label) {
            if (str_contains($lc, $needle)) { $status = $label; break; }
        }

        if (preg_match('/(tsunami\s+(?:information|advisory|warning)\s*(?:no\.?|number)?\s*[:#]?\s*)(\d{1,4})/i', $text, $m)) {
            $bulletinNo = strtoupper(trim($m[1])) . trim($m[2]);
            $bulletinNo = preg_replace('/\s+/', ' ', $bulletinNo);
        } elseif (preg_match('/\bno\.?\s*(\d{1,4})\b/i', $text, $m)) {
            $bulletinNo = 'No. ' . $m[1];
        }

        if (preg_match('/issued\s+(?:at|time)\s*[:\-]?\s*([0-9]{1,2}:[0-9]{2}\s*(?:AM|PM))\s*[,\-]?\s*([0-9]{1,2}\s+[A-Za-z]+\s+[0-9]{4})/i', $text, $m)) {
            $issuedAtRaw = trim($m[1] . ', ' . $m[2]);
            $issuedAt = $this->parseIssuedAt($issuedAtRaw);
        } elseif (preg_match('/([0-9]{1,2}\s+[A-Za-z]+\s+[0-9]{4})\s*[,;\-]?\s*([0-9]{1,2}:[0-9]{2}\s*(?:AM|PM))/i', $text, $m)) {
            $issuedAtRaw = trim($m[1] . ' ' . $m[2]);
            $issuedAt = $this->parseIssuedAt($issuedAtRaw);
        }

        $summary = mb_substr($text, 0, 800);

        return [
            'status' => $status,
            'bulletin_no' => $bulletinNo,
            'issued_at' => $issuedAt,
            'issued_at_raw' => $issuedAtRaw,
            'summary_text' => $summary,
        ];
    }

    private function parseIssuedAt(string $raw): ?Carbon
    {
        $raw = trim(preg_replace('/\s+/', ' ', $raw));
        $tz = 'Asia/Manila';

        try { return Carbon::createFromFormat('h:i A, d F Y', $raw, $tz); } catch (\Throwable $e) {}
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

        Log::info("PHIVOLCS tsunami debug saved", ['tag' => $tag, 'url' => $url]);
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
