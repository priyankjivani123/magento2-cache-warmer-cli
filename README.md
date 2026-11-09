# Magento 2 Full Page Cache Warmer CLI Extension

[![Magento 2.4.x](https://img.shields.io/badge/Magento-2.4.x-orange.svg?logo=magento)](https://business.adobe.com/products/magento/magento-commerce.html)
[![PHP 8.1 - 8.4 Compatible](https://img.shields.io/badge/PHP-8.1%20%7C%208.2%20%7C%208.3%20%7C%208.4-777BB4.svg?logo=php)](https://www.php.net/)
[![Composer Package](https://img.shields.io/badge/Composer-priyankjivani%2Fmodule--cache--warmer--cli-blue.svg?logo=composer)](https://packagist.org/)
[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE)
[![Upwork](https://img.shields.io/badge/Upwork-Available%20for%20Hire-14a800.svg?logo=upwork)](https://www.upwork.com/freelancers/priyankjivani)

High-performance, multi-threaded cache warmer CLI command for **Magento 2 & Adobe Commerce**. Dramatically boosts Varnish and Full Page Cache (FPC) hit ratios after deployments, index invalidations, or catalog imports by asynchronously pre-warming store URLs.

Developed and maintained by **[Priyank Jivani](https://github.com/priyankjivani123)** (Senior Magento 2 Backend Developer).

---

## 🚀 Key Features

* **Multi-Threaded Asynchronous Crawler:** Uses PHP's `curl_multi_init` non-blocking socket pool for high-concurrency requests without blocking the PHP execution thread.
* **Automatic Catalog URL Discovery:** Intelligently extracts canonical URLs across enabled Products, Active Categories, and CMS Pages via `Magento_UrlRewrite`.
* **Sitemap.xml Fallback Support:** Can parse external or local `sitemap.xml` feeds for high-volume catalogs.
* **Configurable Concurrency & Rate Limiting:** Customize concurrency levels (default: 10 parallel threads) and batch chunk sizes to prevent server load spikes.
* **Interactive Symfony Console Output:** Features real-time progress bars, response time metrics, and HTTP status code summaries.

---

## 🌐 Magento & PHP Compatibility Matrix

| Magento / Adobe Commerce Release | Supported PHP Versions | Compatibility Status | Notes |
| :--- | :--- | :---: | :--- |
| **Magento 2.4.4 - 2.4.5-p1** | PHP 8.1 | ✅ Supported | Tested on 2.4.4-p9 & 2.4.5-p1 |
| **Magento 2.4.5-p2 - 2.4.6-pX** | PHP 8.1, PHP 8.2 | ✅ Supported | Tested on 2.4.6-p8 |
| **Magento 2.4.7 - 2.4.7-pX** | PHP 8.2, PHP 8.3 | ✅ Supported | Officially verified on 2.4.7-p3 |
| **Magento 2.4.8 (Roadmap)** | PHP 8.3, PHP 8.4 | 🚀 Forward-Compatible | Strict types, zero implicit nullable deprecations |
| **PHP 8.5 (Future Release)** | *N/A (Pending Adobe Core)* | ⏳ Preview / Audited | Extension syntax passes PHP 8.5 compilation; awaiting Adobe Commerce core framework update |


---

## 📦 Composer Installation Instructions

### Step 1: Register GitHub VCS Repository
```bash
composer config repositories.priyankjivani-cache-warmer-cli vcs https://github.com/priyankjivani123/magento2-cache-warmer-cli.git
```

### Step 2: Require the Package
```bash
composer require priyankjivani/module-cache-warmer-cli:^1.0.0
```

### Step 3: Enable the Module
```bash
bin/magento module:enable PriyankJivani_CacheWarmerCli
bin/magento setup:upgrade
bin/magento cache:flush
```

---

## 💻 CLI Usage & Commands

### 1. Warm Default Store Cache
```bash
bin/magento cache:warmer:run
```

### 2. Warm Specific Store View with 20 Parallel Threads
```bash
bin/magento cache:warmer:run --store=1 --concurrency=20 --limit=500
```

### 3. Warm from Sitemap Feed
```bash
bin/magento cache:warmer:run --sitemap=https://example.com/sitemap.xml
```

---

## 🧪 Automated Testing

```bash
vendor/bin/phpunit -c phpunit.xml.dist
```

---

## 📋 Changelog

See [CHANGELOG.md](CHANGELOG.md).

---

## 📄 License

MIT License. See [LICENSE](LICENSE).

---

## Developing a Magento 2 project?

I help businesses build, customize, optimize and maintain Magento 2 / Adobe Commerce stores.

**Available for:**
- Magento 2 Custom Development
- Magento Extensions
- Bug Fixes and Troubleshooting
- Performance Optimization
- Hyvä Theme Development
- API Integrations
- Ongoing Magento Maintenance

**Hire me on Upwork:** [Priyank Jivani Upwork Profile](https://www.upwork.com/freelancers/priyankjivani)  
**GitHub:** [github.com/priyankjivani123](https://github.com/priyankjivani123)

Contact me for freelance projects and long-term collaborations.

