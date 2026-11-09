# Magento 2 Full Page Cache Warmer CLI (Varnish & Built-in FPC)

[![Magento 2.4.x](https://img.shields.io/badge/Magento-2.4.x-orange.svg?logo=magento)](https://business.adobe.com/products/magento/magento-commerce.html)
[![PHP 8.1 | 8.2 | 8.3](https://img.shields.io/badge/PHP-8.1%20%7C%208.2%20%7C%208.3-777BB4.svg?logo=php)](https://www.php.net/)
[![Varnish Compatible](https://img.shields.io/badge/Varnish-Compatible-blue.svg)](https://varnish-cache.org/)
[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Upwork Top Rated](https://img.shields.io/badge/Upwork-Available%20for%20Hire-green.svg?logo=upwork)](https://www.upwork.com/freelancers/priyankjivani)

A high-performance store optimization tool for **Magento 2 & Adobe Commerce**. Pre-warms Magento Full Page Cache (FPC) and Varnish reverse proxies after deployment, code releases, catalog updates, or cache flushes using non-blocking multi-threaded asynchronous HTTP crawling.

Developed and maintained by **[Priyank Jivani](https://github.com/priyankjivani123)** (Senior Magento 2 Backend Developer).

---

## ⚡ The Problem: Cache Misses Hurt Conversions

Every time you flush your Magento cache or deploy updates:
1. **First-time shoppers suffer slow TTFB (Time to First Byte)** of 2–5 seconds while Magento compiles layout XML and blocks on the fly.
2. **Server CPU and MySQL loads spike** drastically under un-cached traffic.
3. **Core Web Vitals scores and Google SEO rankings drop** due to slow Largest Contentful Paint (LCP).

**`priyank/module-cache-warmer-cli`** warms your catalog in minutes before actual shoppers visit your site.

---

## 🚀 Key Features

* **Multi-Threaded Asynchronous Crawling**: Leverages non-blocking `curl_multi` handles to fetch multiple store pages in parallel without bottlenecking server resources.
* **Automatic Store URL Discovery**: Automatically queries active canonical product, category, and CMS URLs directly from Magento's `url_rewrite` table.
* **XML Sitemap Integration**: Can read and warm URLs directly from standard XML sitemaps (`sitemap.xml`).
* **Live Terminal Progress Bar**: Interactive console progress bar with real-time status code reporting, duration, and URL tracking.
* **Full Diagnostic Metrics**: Outputs a structured summary table reporting total URLs crawled, 200 OK responses, redirects, failures, elapsed time, and average TTFB per page.
* **Compatible with Varnish, Fastly & Built-in FPC**: Pre-populates cache keys identically for guest shoppers.

---

## 💻 CLI Usage

### Standard Full-Page Cache Warming

```bash
bin/magento cache:warmer:run
```

### Advanced Options:

```bash
# Warm with 10 concurrent parallel workers (faster crawling)
bin/magento cache:warmer:run --concurrency=10

# Limit the number of URLs to warm (e.g. top 500 pages)
bin/magento cache:warmer:run --limit=500

# Target a specific Store View (Store ID or Code)
bin/magento cache:warmer:run --store=1

# Warm URLs defined in an XML Sitemap
bin/magento cache:warmer:run --sitemap=pub/media/sitemap.xml
```

---

## 🛠 Technical Requirements

| Stack Component | Requirement |
| :--- | :--- |
| **Magento / Adobe Commerce** | 2.4.4, 2.4.5, 2.4.6, 2.4.7+ |
| **PHP** | 8.1, 8.2, 8.3 (with `curl` extension) |
| **Caching Tier** | Varnish 6.x / 7.x, Fastly, or Built-in Page Cache |
| **Composer** | 2.x |

---

## 📦 Installation Instructions

### Option 1: Composer Installation (Recommended)

```bash
composer require priyank/module-cache-warmer-cli
bin/magento module:enable Priyank_CacheWarmer
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

### Option 2: Manual Installation

1. Create directory `app/code/Priyank/CacheWarmer`.
2. Extract the repository files into `app/code/Priyank/CacheWarmer`.
3. Enable and compile:
```bash
bin/magento module:enable Priyank_CacheWarmer
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

---

## 🏗 Architecture & Code Structure

```
├── Console/
│   └── Command/
│       └── WarmCacheCommand.php   # bin/magento cache:warmer:run
├── Model/
│   ├── Crawler.php                # Asynchronous multi-curl HTTP pool worker
│   └── UrlProvider.php            # Database URL rewrite collection & XML sitemap parser
├── etc/
│   ├── di.xml                     # CommandList dependency injection
│   └── module.xml
├── composer.json
├── registration.php
└── README.md
```

---

## 📄 License

Open-source module released under the [MIT License](LICENSE).

---

## 💼 Developing a Magento 2 project?

I help businesses build, customize, optimize, and maintain **Magento 2 / Adobe Commerce** stores.

**Available for:**
* Magento 2 Custom Development & Architecture
* Custom Magento Extensions & Modules
* Bug Fixes, Audits, and Troubleshooting
* Performance Optimization & Core Web Vitals
* Hyvä Theme Development & Customization
* Headless GraphQL & REST API Integrations
* Ongoing Magento Store Maintenance

👉 **Hire me on Upwork:** [Priyank Jivani on Upwork](https://www.upwork.com/freelancers/priyankjivani)  
👉 **GitHub Profile:** [priyankjivani123](https://github.com/priyankjivani123)

Contact me for freelance projects and long-term enterprise collaborations.
