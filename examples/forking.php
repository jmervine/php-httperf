<?php
require_once "HTTPerf.php";

$httperf = new HTTPerf(array(
  "server"    => "www.example.com",
  "rate"      => 5,
  "num-conns" => 10,
  "parse"     => true,
  "verbose"   => true
));

echo "Running: ";
echo $httperf->command() . "\n";

/**
 * Example 1 - Fork and wait.
 ******************************************/
$proc = $httperf->fork();
print_r($httperf->forkWait(1, function() {
  echo "forkWait: waiting...\n";
}));

echo "----\n";
echo "Running: ";
echo $httperf->command() . "\n";

/**
 * Example 1 - Fork check if running.
 ******************************************/
$proc = $httperf->fork();
while ($httperf->forkRunning()) {
  echo "fork running...\n";
  sleep(1);
}

print_r($httperf->result);
