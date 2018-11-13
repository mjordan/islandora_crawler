# Islandora Crawler

First attempt at a script to crawl an Islandora 7.x site to get all objects' URLs.

## Requirements

* PHP 7.1.3 or higher
* [composer](https://getcomposer.org/)

### Installation

1. Clone this git repository
1. `cd islandora_crawler`
1. `php composer.phar install` (or equivalent on your system, e.g., `./composer install`)

## Usage

In the `crawler.php` script, adjust the `$dsid` and `$collection_url` variables:

```
/**
 * You may need to adjust these two variables.
 */
$dsid = 'MODS';
$collection_url = 'http://192.168.0.120:8000/islandora/object/islandora%3Asp_basic_image_collection';
```

Then, run the `crawler.php` script:

`php crawler.php`

## License

Unlicense.
