<?php

/**
 * You may need to adjust these three variables.
 */
$dsid = 'MODS';
// Do not include the trailing /.
$base_url = 'http://192.168.0.120:8000';
$collection_pid = 'islandora:sp_basic_image_collection'; 

/**
 * You do not need to adjust anything below this line.
 */
require_once __DIR__ . '/vendor/autoload.php';
use Goutte\Client;
$client = new Client();

// Get the last page in the collection browse.
$collection_url = $base_url . '/islandora/object/' . $collection_pid;
$crawler = $client->request('GET', $collection_url);
$last_page_url = $crawler->filter('li.pager-last > a')->extract(array('href'));
$params = parse_url($last_page_url[0], PHP_URL_QUERY);
$params = parse_str($params, $pages);
$pages = range(0, $pages['page']);
$object_urls = array();

print "Scraping URLs for the $dsid DSID for the collection starting at $collection_url...\n";

// Scrape each of the parameterized browse pages defined in $pages.
foreach ($pages as $page) {
    $browse_url = $collection_url . '?page=' . $page;
    $crawler = $client->request('GET', $browse_url);
    $hrefs = $crawler->filter('dl > dt > a')->extract(array('href'));
    $object_urls = array_merge($object_urls, $hrefs);
}

// Extract the PID from each object URL. This will be specific to the URLs on the site
// e.g., specific to path auto URL patterns, etc.
foreach ($object_urls as &$url) {
    $url = urldecode($url);
    $url_parts = explode('/', $url);
    $pid = $url_parts[2];
    $ds_download_url = $base_url . $url . '/datastream/' . $dsid . '/download';
    print $ds_download_url . "\n";
}

$count = count($object_urls);
print "Scraped $count URLs!\n";
