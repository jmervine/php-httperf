<?php
require_once "HTTPerf.php";

$options = array(
    "server"    => "www.example.com",
    "rate"      => 1,
    "num-conns" => 10,
    "verbose"   => true,
    "hog"       => true,
    "verbose"   => true,
    "parse"     => false
);

$httperf = new HTTPerf($options);
$results = $httperf->run();
print $results."\n";

