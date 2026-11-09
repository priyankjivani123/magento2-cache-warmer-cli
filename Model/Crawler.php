<?php
declare(strict_types=1);

namespace Priyank\CacheWarmer\Model;

class Crawler
{
    /**
     * Crawl an array of URLs using multi-curl concurrent requests
     *
     * @param array $urls
     * @param int $concurrency
     * @param string $userAgent
     * @param callable|null $onProgress Callback executed after each response: fn(string $url, int $status, float $time)
     * @return array
     */
    public function crawlUrls(
        array $urls,
        int $concurrency = 5,
        string $userAgent = 'Magento-Cache-Warmer-Priyank/1.0',
        ?callable $onProgress = null
    ): array {
        $totalStartTime = microtime(true);
        $queue = $urls;
        $activeHandles = [];
        $results = [
            'total'    => count($urls),
            'success'  => 0,
            'redirect' => 0,
            'failed'   => 0,
            'details'  => []
        ];

        $multiHandle = curl_multi_init();

        // Helper to add handle to curl_multi
        $addHandle = function (string $url) use ($multiHandle, &$activeHandles, $userAgent) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_NOBODY, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
            curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            curl_multi_add_handle($multiHandle, $ch);
            $key = (int) $ch;
            $activeHandles[$key] = [
                'handle'    => $ch,
                'url'       => $url,
                'startTime' => microtime(true)
            ];
        };

        // Seed initial pool
        while (!empty($queue) && count($activeHandles) < $concurrency) {
            $addHandle(array_shift($queue));
        }

        do {
            $status = curl_multi_exec($multiHandle, $running);

            while ($info = curl_multi_info_read($multiHandle)) {
                $ch = $info['handle'];
                $key = (int) $ch;
                $urlData = $activeHandles[$key];
                $url = $urlData['url'];
                $duration = round(microtime(true) - $urlData['startTime'], 3);
                $httpCode = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($httpCode >= 200 && $httpCode < 300) {
                    $results['success']++;
                } elseif ($httpCode >= 300 && $httpCode < 400) {
                    $results['redirect']++;
                } else {
                    $results['failed']++;
                }

                $results['details'][] = [
                    'url'      => $url,
                    'status'   => $httpCode,
                    'duration' => $duration
                ];

                if ($onProgress) {
                    $onProgress($url, $httpCode, $duration);
                }

                curl_multi_remove_handle($multiHandle, $ch);
                curl_close($ch);
                unset($activeHandles[$key]);

                // Pull next URL from queue
                if (!empty($queue)) {
                    $addHandle(array_shift($queue));
                }
            }

            if ($running > 0) {
                curl_multi_select($multiHandle, 0.05);
            }
        } while ($running > 0 || !empty($activeHandles));

        curl_multi_close($multiHandle);

        $results['elapsed_time'] = round(microtime(true) - $totalStartTime, 2);
        return $results;
    }
}
