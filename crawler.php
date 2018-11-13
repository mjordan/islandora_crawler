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

$page_count = 0;
$url_count = 0;

print "Scraping URLs for the $dsid DSID for the collection starting at $collection_url...\n";

// Scrape each of the parameterized browse pages defined in $pages.
foreach ($pages as $page) {
    $page_count++;
    $browse_url = $collection_url . '?page=' . $page;
    $crawler = $client->request('GET', $browse_url);
    // We scrape the TN 'src' URL because it is not affected by URL aliases.
    $hrefs = $crawler->filter('dl > dt img')->extract(array('src'));
    foreach ($hrefs as $href) {
        $dsid_url = href_to_dsid_url($href);
        $url_count++;
        // Instead of just printing the download URL, we could download and save it for analysis later.
        print $dsid_url ."\n";
    }
}

print "Scraped $url_count URLs from $page_count pages.\n";

function href_to_dsid_url ($href) {
    global $dsid;
    $href = urldecode($href);
    // Swap out 'TN' for the DSID configured above.
    $ds_download_url = preg_replace('/TN/', $dsid, $href);
    $ds_download_url = preg_replace('/view$/', 'download', $ds_download_url);
    return $ds_download_url;
}
