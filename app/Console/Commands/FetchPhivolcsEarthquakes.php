<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\PhivolcsEarthquake;
use Carbon\Carbon;

class FetchPhivolcsEarthquakes extends Command
{
    protected $signature = 'phivolcs:fetch-earthquakes
        {--limit=5 : Number of rows to store}
        {--insecure : Disable TLS peer verification (temporary workaround)}
        {--debug : Print verbose debug info}
        {--cafile= : CA bundle path for TLS verification (e.g. /usr/local/etc/ssl/cacert.pem)}';

    protected $description = 'Fetch latest earthquakes from PHIVOLCS via raw TLS socket (redirect-aware) and store.';

    public function handle(): int
    {
        $seedUrl = 'https://www.phivolcs.dost.gov.ph/earthquake-information/';
        $fetchedAt = now();
        $limit = max(1, (int)$this->option('limit'));
        $insecure = (bool)$this->option('insecure');

        $seedRes = $this->fetchUrlFollowRedirects($seedUrl, 'text/html', $insecure);

        if (!$seedRes['ok']) {
            $this->printFailure('seed', $seedRes, $seedUrl);
            $this->error('Failed to fetch seed HTML (raw socket).');
            return self::FAILURE;
        }

        $seedHtml = $seedRes['body'];
        $seedFinalUrl = $seedRes['final_url'] ?? $seedUrl;

        $parsed = $this->parseEarthquakesFromHtml($seedHtml, $seedFinalUrl, $fetchedAt, $limit);

        if ($parsed['saved'] > 0) {
            $this->info("Saved/updated {$parsed['saved']} earthquake(s) from seed page.");
            return self::SUCCESS;
        }

        $targetUrl = $this->findEarthquakeSourceUrl($seedHtml);

        if (!$targetUrl) {
            $this->warn('No iframe/link/url found in seed HTML. Fallback to known earthquake site.');
            $targetUrl = 'https://earthquake.phivolcs.dost.gov.ph/';
        }

        $targetRes = $this->fetchUrlFollowRedirects($targetUrl, 'text/html', $insecure);

        if (!$targetRes['ok']) {
            $this->saveDebugFiles($seedHtml, $seedFinalUrl, $parsed['text'] ?? '', 'seed_no_matches');
            $this->printFailure('target', $targetRes, $targetUrl);
            $this->error("Failed to fetch target HTML: {$targetUrl}");
            return self::FAILURE;
        }

        $targetHtml = $targetRes['body'];
        $targetFinalUrl = $targetRes['final_url'] ?? $targetUrl;

        $parsed2 = $this->parseEarthquakesFromHtml($targetHtml, $targetFinalUrl, $fetchedAt, $limit);

        if ($parsed2['saved'] === 0) {
            $this->saveDebugFiles($seedHtml, $seedFinalUrl, $parsed['text'] ?? '', 'seed_no_matches');
            $this->saveDebugFiles($targetHtml, $targetFinalUrl, $parsed2['text'] ?? '', 'target_no_matches');
            $this->warn('Fetched target page but still no matches. Debug saved.');
            return self::SUCCESS;
        }

        $this->info("Saved/updated {$parsed2['saved']} earthquake(s) from target page.");
        return self::SUCCESS;
    }

    private function printFailure(string $label, array $res, string $url): void
    {
        Log::warning("PHIVOLCS fetch failed ({$label})", $res + ['url' => $url]);

        $this->line("---- {$label} fetch failure debug ----");
        $this->line("URL: {$url}");
        foreach (['stage','errno','errstr','status','statusLine'] as $k) {
            if (isset($res[$k])) $this->line("{$k}: " . (is_scalar($res[$k]) ? $res[$k] : json_encode($res[$k])));
        }
        if (!empty($this->option('debug'))) {
            foreach (['headers','rawHeaderHead','rawHead','bodyHead','meta'] as $k) {
                if (isset($res[$k])) {
                    $val = is_scalar($res[$k]) ? $res[$k] : json_encode($res[$k], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    $this->line("{$k}: {$val}");
                }
            }
        }
        $this->line("------------------------------------");
    }

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

        return [
            'ok' => false,
            'stage' => 'redirect_loop',
            'final_url' => $current,
        ];
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
            $cafile = (string)$this->option('cafile');
            if ($cafile !== '' && is_file($cafile)) {
                $ssl['cafile'] = $cafile;
            }
        }

        $context = stream_context_create(['ssl' => $ssl]);

        $fp = @stream_socket_client($remote, $errno, $errstr, 25, STREAM_CLIENT_CONNECT, $context);

        if (!$fp) {
            return [
                'ok' => false,
                'stage' => 'connect',
                'errno' => $errno,
                'errstr' => $errstr,
                'host' => $host,
            ];
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
        if ($pos === false) {
            return [
                'ok' => false,
                'stage' => 'split',
                'rawHead' => mb_substr($raw, 0, 500),
                'meta' => $meta,
            ];
        }

        $rawHeader = substr($raw, 0, $pos);
        $body = substr($raw, $pos + 4);

        $lines = preg_split("/\r\n/", $rawHeader);
        $statusLine = $lines[0] ?? '';

        if (!preg_match('~^HTTP/\d\.\d\s+(\d{3})~', $statusLine, $sm)) {
            return [
                'ok' => false,
                'stage' => 'status_parse',
                'statusLine' => $statusLine,
                'rawHeaderHead' => mb_substr($rawHeader, 0, 500),
                'meta' => $meta,
            ];
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

        if ($status < 200 || $status >= 400) {
            return [
                'ok' => false,
                'stage' => 'non2xx',
                'status' => $status,
                'headers' => $headers,
                'bodyHead' => mb_substr($body, 0, 300),
                'meta' => $meta,
            ];
        }

        return [
            'ok' => true,
            'status' => $status,
            'headers' => $headers,
            'body' => $body,
            'meta' => $meta,
        ];
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

    private function parseEarthquakesFromHtml(string $html, string $sourceUrl, $fetchedAt, int $limit): array
    {
        $cleanHtml = preg_replace('~<script\b[^>]*>.*?</script>~is', ' ', $html);
        $cleanHtml = preg_replace('~<style\b[^>]*>.*?</style>~is', ' ', $cleanHtml);

        $text = strip_tags($cleanHtml);
        $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        $matches = [];

        preg_match_all(
            '/(\d{2}\s+[A-Za-z]+\s+\d{4})\s*-\s*(\d{1,2}:\d{2})\s*(AM|PM)\s+([0-9.]+)\s+([0-9.]+)\s+(\d{1,3})\s+([0-9.]+)\s+(.+?)(?=\s+\d{2}\s+[A-Za-z]+\s+\d{4}\s*-\s*\d{1,2}:\d{2}\s*(?:AM|PM)\s+|$)/i',
            $text,
            $rows,
            PREG_SET_ORDER
        );

        foreach ($rows as $r) {
            $occurredAtRaw = trim($r[1] . ' - ' . $r[2] . ' ' . $r[3]);
            $matches[] = [
                'occurred_at_raw' => $occurredAtRaw,
                'lat' => (float)$r[4],
                'lng' => (float)$r[5],
                'depth' => (float)$r[6],
                'mag' => (float)$r[7],
                'location' => trim(preg_replace('/\s+/', ' ', $r[8])),
            ];
        }

        $saved = 0;
        foreach (array_slice($matches, 0, $limit) as $row) {
            $occurredAt = $this->parseDateTime($row['occurred_at_raw']);
            $hash = sha1(json_encode($row, JSON_UNESCAPED_UNICODE));

            PhivolcsEarthquake::updateOrCreate(
                ['hash' => $hash],
                [
                    'occurred_at' => $occurredAt,
                    'lat' => $row['lat'],
                    'lng' => $row['lng'],
                    'magnitude' => $row['mag'],
                    'depth_km' => $row['depth'],
                    'location_text' => $row['location'],
                    'source_url' => $sourceUrl,
                    'issued_at' => $occurredAt,
                    'fetched_at' => $fetchedAt,
                ]
            );

            $saved++;
        }

        return ['saved' => $saved, 'text' => $text];
    }

    private function findEarthquakeSourceUrl(string $html): ?string
    {
        if (preg_match('~<iframe\b[^>]*\bsrc=["\']([^"\']+)["\']~i', $html, $m)) {
            return $this->normalizeUrl($m[1]);
        }

        if (preg_match_all('~<a\b[^>]*\bhref=["\']([^"\']+)["\']~i', $html, $mm)) {
            foreach ($mm[1] as $href) {
                $u = $this->normalizeUrl($href);
                if (stripos($u, 'earthquake.phivolcs') !== false) return $u;
            }
        }

        if (preg_match('~https?://[^\s"\']*earthquake\.phivolcs[^\s"\']*~i', $html, $m2)) return $m2[0];
        if (preg_match('~//[^\s"\']*earthquake\.phivolcs[^\s"\']*~i', $html, $m3)) return 'https:' . $m3[0];

        if (stripos($html, 'earthquake.phivolcs') !== false) return 'https://earthquake.phivolcs.dost.gov.ph/';

        return null;
    }

    private function normalizeUrl(string $url): string
    {
        $url = trim($url);
        if (str_starts_with($url, '//')) return 'https:' . $url;
        if (str_starts_with($url, 'http://') || str_starts_with($url, 'https://')) return $url;
        if (str_starts_with($url, '/')) return 'https://www.phivolcs.dost.gov.ph' . $url;
        return 'https://www.phivolcs.dost.gov.ph/' . ltrim($url, '/');
    }

    private function saveDebugFiles(string $html, string $sourceUrl, string $text, string $tag): void
    {
        $dir = 'phivolcs_debug';
        Storage::makeDirectory($dir);

        $stamp = now()->format('Ymd_His');
        Storage::put("{$dir}/earthquake_{$tag}_{$stamp}.html", $html);
        Storage::put("{$dir}/earthquake_{$tag}_{$stamp}.txt", $text);

        Log::warning("PHIVOLCS earthquake debug saved ({$tag})", [
            'url' => $sourceUrl,
            'tag' => $tag,
        ]);
    }

    private function splitUrl(string $url): array
    {
        $p = parse_url($url);
        $host = $p['host'] ?? '';
        $path = ($p['path'] ?? '/') . (isset($p['query']) ? ('?' . $p['query']) : '');
        return [$host, $path];
    }

    private function parseDateTime(string $raw)
    {
        $raw = trim(preg_replace('/\s+/', ' ', $raw));
        $tz = 'Asia/Manila';

        try { return Carbon::createFromFormat('d F Y - h:i A', $raw, $tz); } catch (\Throwable $e) {}
        try { return Carbon::createFromFormat('d M Y - g:i A', $raw, $tz); } catch (\Throwable $e) {}
        try { return Carbon::parse($raw, $tz); } catch (\Throwable $e) { return null; }
    }
}
