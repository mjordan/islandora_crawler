<?php

/**
 * You may need to adjust these two variables.
 */
$dsid = 'MODS';
$collection_url = 'http://192.168.0.120:8000/islandora/object/islandora%3Asp_basic_image_collection';

/**
 * You do not need to adjust anything below this line.
 */
require_once __DIR__ . '/vendor/autoload.php';
use Goutte\Client;
$client = new Client();

// Get the last page in the collection browse.
$url_parts = parse_url($collection_url);
$site_base_url = $url_parts['scheme'] . '//' . $url_parts['host'] . ':' . $url_parts['port'];
$crawler = $client->request('GET', $collection_url);
$last_page_url = $crawler->filter('li.pager-last > a')->extract(array('href'));
$params = parse_url($last_page_url[0], PHP_URL_QUERY);
$params = parse_str($params, $pages);
$pages = range(0, $pages['page']);
$object_urls = array();

print "Scraping object URLs (DSID $dsid) from pages starting at $collection_url...\n";

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
    $url = ltrim($url, '/');
    $pid = preg_replace('#/.*$#', '', $url);
    $pid = preg_replace('#\-#', ':', $pid);
    $rels_ext_url = $site_base_url . '/islandora/object/' . $pid . '/datastream/' . $dsid . '/download';
    print $rels_ext_url . "\n";
}

$count = count($object_urls);
print "Processed $count URLs\n";
