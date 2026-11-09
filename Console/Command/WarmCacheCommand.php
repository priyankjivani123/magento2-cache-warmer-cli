<?php
declare(strict_types=1);

namespace PriyankJivani\CacheWarmerCli\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Helper\Table;
use PriyankJivani\CacheWarmerCli\Model\UrlProvider;
use PriyankJivani\CacheWarmerCli\Model\Crawler;

class WarmCacheCommand extends Command
{
    private const OPTION_STORE = 'store';
    private const OPTION_LIMIT = 'limit';
    private const OPTION_CONCURRENCY = 'concurrency';
    private const OPTION_SITEMAP = 'sitemap';

    /**
     * @var UrlProvider
     */
    private $urlProvider;

    /**
     * @var Crawler
     */
    private $crawler;

    public function __construct(
        UrlProvider $urlProvider,
        Crawler $crawler,
        ?string $name = null
    ) {
        parent::__construct($name);
        $this->urlProvider = $urlProvider;
        $this->crawler = $crawler;
    }

    protected function configure(): void
    {
        $this->setName('cache:warmer:run')
            ->setDescription('Pre-warm Magento 2 Full Page Cache (FPC) and Varnish with multi-threaded concurrent requests.')
            ->addOption(
                self::OPTION_STORE,
                's',
                InputOption::VALUE_OPTIONAL,
                'Target Store ID or Code'
            )
            ->addOption(
                self::OPTION_LIMIT,
                'l',
                InputOption::VALUE_OPTIONAL,
                'Maximum number of URLs to crawl (default: 100)',
                100
            )
            ->addOption(
                self::OPTION_CONCURRENCY,
                'c',
                InputOption::VALUE_OPTIONAL,
                'Number of concurrent parallel HTTP workers (default: 5)',
                5
            )
            ->addOption(
                self::OPTION_SITEMAP,
                'm',
                InputOption::VALUE_OPTIONAL,
                'Path to XML sitemap file to parse URLs from (relative to Magento root or pub/)'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $storeId = $input->getOption(self::OPTION_STORE) !== null ? (int) $input->getOption(self::OPTION_STORE) : null;
        $limit = (int) $input->getOption(self::OPTION_LIMIT);
        $concurrency = (int) $input->getOption(self::OPTION_CONCURRENCY);
        $sitemapPath = $input->getOption(self::OPTION_SITEMAP);

        $output->writeln('<info>Collecting Store URLs for Cache Warming...</info>');

        try {
            if ($sitemapPath) {
                $urls = $this->urlProvider->getUrlsFromSitemap($sitemapPath);
                $urls = array_slice($urls, 0, $limit);
            } else {
                $urls = $this->urlProvider->getStoreUrls($storeId, $limit);
            }
        } catch (\Throwable $e) {
            $output->writeln("<error>Failed to collect URLs: {$e->getMessage()}</error>");
            return Command::FAILURE;
        }

        if (empty($urls)) {
            $output->writeln('<comment>No eligible URLs found to warm.</comment>');
            return Command::SUCCESS;
        }

        $totalUrls = count($urls);
        $output->writeln("<info>Found {$totalUrls} URLs to warm using {$concurrency} concurrent workers.</info>\n");

        $progressBar = new ProgressBar($output, $totalUrls);
        $progressBar->setFormat(' %current%/%max% [%bar%] %percent:3s%% -- %message%');
        $progressBar->setMessage('Initializing crawler...');
        $progressBar->start();

        $results = $this->crawler->crawlUrls(
            $urls,
            $concurrency,
            'Magento-Cache-Warmer-Priyank/1.0',
            function (string $url, int $status, float $time) use ($progressBar) {
                $shortUrl = strlen($url) > 50 ? '...' . substr($url, -47) : $url;
                $progressBar->setMessage("HTTP {$status} ({$time}s) - {$shortUrl}");
                $progressBar->advance();
            }
        );

        $progressBar->setMessage('<info>Completed!</info>');
        $progressBar->finish();
        $output->writeln("\n");

        // Summary Table
        $table = new Table($output);
        $table->setHeaders(['Metric', 'Value']);
        $table->addRow(['Total URLs Crawled', $results['total']]);
        $table->addRow(['Successful (HTTP 2xx)', "<info>{$results['success']}</info>"]);
        $table->addRow(['Redirects (HTTP 3xx)', "<comment>{$results['redirect']}</comment>"]);
        $table->addRow(['Failed (HTTP 4xx/5xx)', ($results['failed'] > 0 ? "<error>{$results['failed']}</error>" : '0')]);
        $table->addRow(['Total Time Elapsed', "{$results['elapsed_time']} seconds"]);

        $avgTime = $results['total'] > 0 ? round($results['elapsed_time'] / $results['total'], 3) : 0;
        $table->addRow(['Avg Response per Page', "{$avgTime} seconds"]);

        $table->render();
        $output->writeln('<info>✓ Full-Page Cache Warming Cycle Finished Successfully!</info>');

        return Command::SUCCESS;
    }
}
