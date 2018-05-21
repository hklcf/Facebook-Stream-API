<?php
/*
    Version: 1.0
    Author: HKLCF
    Copyright: HKLCF
    Last Modified: 21/05/2018
*/

$url = isset($_GET['url']) ? htmlspecialchars($_GET['url']) : null;
$support_domain = 'www.facebook.com';

if(empty($url)) {
  $url = 'https://www.facebook.com/facebook/videos/10153231379946729/'; // sample link
}
if($url) {
  preg_match('@^(?:http.?://)?([^/]+)@i', $url, $matches);
  $host = $matches[1];
  if($host != $support_domain) {
    echo 'Please input a valid facebook url.';
    exit;
  }
}

$api = 'https://www.facebook.com/plugins/video.php?href=';
$result = file_get_contents($api.$url, false, stream_context_create(['socket' => ['bindto' => '0:0'], 'http' => ['header' => 'User-Agent: Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/66.0.3359.181 Safari/537.36']]));

preg_match_all('/"(sd|hd)_src_no_ratelimit":"([^"]+)"/', $result, $matches);

$quality = $matches[1];
$links = $matches[2];

$label = [];
foreach($quality as $quality_label) {
  $label[] = "$quality_label";
}

$itag = 0;
$output = [];
foreach($links as $direct_link) {
  $direct_link = json_decode('"'.$direct_link.'"');
  $output[] = ['label' => strtoupper($label[$itag]), 'file' => $direct_link, 'type' => 'video/mp4'];
  $itag++;
}

$output = json_encode($output);

echo $output;
?>
