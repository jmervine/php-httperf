<?php
require_once 'HTTPerf.php';

$options = array(
    'server'    => 'www.example.com',
    'rate'      => 1,
    'num-conns' => 10,
    'verbose'   => true,
    'hog'       => true,
    'parse'     => true
);

$httperf = new HTTPerf($options);
$results = $httperf->run();
print_r($results);

