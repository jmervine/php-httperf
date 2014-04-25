HTTPerf.php [![Build Status](https://travis-ci.org/jmervine/php-httperf.svg?branch=master)](https://travis-ci.org/jmervine/php-httperf)
-----------

##### Simple PHP port of [HTTPerf.rb](https://github.com/jmervine/httperfrb)

Simple PHP interface for [httperf](http://mervine.net/httperf).

Tested via [Travis-CI](https://travis-ci.org/jmervine/php-httperf) on:

* PHP 5.3
* PHP 5.4
* PHP 5.5
* HHVM

## Installing 'httperf'

Requires [httperf](http://mervine.net/httperf), of course...

#### Mac

    sudo port install httperf

#### Debian / Ubuntu

    sudo apt-get install httperf

#### Redhat / CentOS

    sudo yum install httperf

#### My 'httperf'

See: [httperf-0.9.1 with individual connection times](http://mervine.net/httperf-0-9-1-with-individual-connection-times).


## Basic Usage

``` php
<?php
require_once 'HTTPerf.php';

$options = array(
    "server"    => "www.example.com",
    "uri"       => "/foo/bar",
    "rate"      => 10,
    "num-conns" => 1000,
    "verbose"   => true,
    "hog"       => true,
    "parse"     => true
);

$httperf = new HTTPerf($options);
$results = $httperf->run();
print_r($results);

$httperf->updateOptions("uri", "www.google.com");
$results = $httperf->run();
print_r($results);

```

#### Forking

``` php
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

```

## Development

Please feel free to submit pull requests as this is my first stab at PHP in about 10 years. Before submitting a pull request, though, please make sure to update (if necessary) and run unit tests.

```
make test
```

