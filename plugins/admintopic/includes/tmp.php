<?php

  // content of somefile.php

include('pr.php');

$url='http://goloskarpat.info/profile/novynyzakarpattya/';
$pr = new PR();
echo "$url has Google PageRank: ". $pr->get_google_pagerank($url) ;


?>
