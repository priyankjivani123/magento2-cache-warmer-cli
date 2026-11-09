<?php
declare(strict_types=1);

namespace Priyank\CacheWarmer\Model;

use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewrite\CollectionFactory as UrlRewriteCollectionFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class UrlProvider
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var UrlRewriteCollectionFactory
     */
    private $urlRewriteCollectionFactory;

    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        StoreManagerInterface $storeManager,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        Filesystem $filesystem
    ) {
        $this->storeManager = $storeManager;
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->filesystem = $filesystem;
    }

    /**
     * Collect URLs from database URL rewrites (products, categories, CMS)
     */
    public function getStoreUrls(?int $storeId = null, int $limit = 200): array
    {
        $store = $this->storeManager->getStore($storeId);
        $baseUrl = rtrim($store->getBaseUrl(), '/');

        $collection = $this->urlRewriteCollectionFactory->create();
        $collection->addFieldToFilter('store_id', $store->getId());
        $collection->addFieldToFilter('redirect_type', 0); // Only canonical 200 URLs
        $collection->addFieldToFilter('entity_type', ['in' => ['product', 'category', 'cms-page']]);
        $collection->setPageSize($limit);

        $urls = [$baseUrl . '/']; // Add Homepage

        foreach ($collection as $rewrite) {
            $urls[] = $baseUrl . '/' . ltrim($rewrite->getRequestPath(), '/');
        }

        return array_unique($urls);
    }

    /**
     * Parse URLs from an XML sitemap file
     */
    public function getUrlsFromSitemap(string $sitemapPath): array
    {
        if (!file_exists($sitemapPath)) {
            $pubDir = $this->filesystem->getDirectoryRead(DirectoryList::PUB)->getAbsolutePath();
            $sitemapPath = $pubDir . ltrim($sitemapPath, '/');
        }

        if (!file_exists($sitemapPath)) {
            throw new \InvalidArgumentException("Sitemap file not found at: {$sitemapPath}");
        }

        $xml = simplexml_load_file($sitemapPath);
        if ($xml === false) {
            throw new \RuntimeException("Failed to parse XML sitemap at: {$sitemapPath}");
        }

        $urls = [];
        foreach ($xml->url as $urlElement) {
            if (isset($urlElement->loc)) {
                $urls[] = (string) $urlElement->loc;
            }
        }

        return array_unique($urls);
    }
}
