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
    'server'    => 'www.example.com',
    'uri'       => '/foo/bar',
    'rate'      => 10,
    'num-conns' => 1000,
    'verbose'   => true,
    'hog'       => true,
    'parse'     => true
);

$httperf = new HTTPerf();
$results = $httperf->run();
print_r($results);

```


## Development

Please feel free to submit pull requests as this is my first stab at PHP in about 10 years. Before submitting a pull request, though, please make sure to update (if necessary) and run unit tests.

```
make test
```

