<?php

use Goutte\Client;
require_once __DIR__ . '/vendor/autoload.php';

$client = new Client();

$browse_url = 'http://digital.lib.sfu.ca/alping-collection/richard-harwood-dick-chambers-alpine-photograph-collection';
$site_base_url = 'http://digital.lib.sfu.ca';
// This range corresponds to the number of pages in th collection's browse list.
$pages = range(0, 68);
$object_urls = array();

print "Scraping object URLs from pages starting at $browse_url...\n";

// Then scrape each of the parameterized browse pages defined in $pages.
foreach ($pages as $page) {
    $crawler = $client->request('GET', $browse_url . '?page=' . $page);
    $hrefs = $crawler->filter('dd.islandora-object-caption > a')->extract(array('href'));
    $object_urls = array_merge($object_urls, $hrefs);
}

// Extract the PID from each object URL. This will be specific to the URLs on the site
// e.g., specific to path auto URL patterns, etc.
foreach ($object_urls as &$url) {
    $url = ltrim($url, '/');
    $pid = preg_replace('#/.*$#', '', $url);
    $pid = preg_replace('#\-#', ':', $pid);
    $rels_ext_url = $site_base_url . '/islandora/object/' . $pid . '/datastream/RELS-EXT/download';
    print $rels_ext_url . "\n";
}

$count = count($object_urls);
print "Processed $count URLs\n";
