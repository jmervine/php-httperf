HTTPerf.php
-----------

##### Simple PHP port of [HTTPerf.rb](https://github.com/jmervine/httperfrb)

Simple PHP interface for httperf.

## Installing 'httperf'

Requires [httperf](http://mervine.net/httperf), of course...

#### Mac

    :::shell
    sudo port install httperf

#### Debian / Ubuntu

    :::shell
    sudo apt-get install httperf

#### Redhat / CentOS

    :::shell
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
