<?php

$root = dirname(__FILE__) . "/";
require_once $root . "support/simpletest/autorun.php";
require_once $root . "../HTTPerf.php";
require_once $root . "../Parser.php";

class TestHelper {
  public static function dir() {
    return dirname(__FILE__) . "/";
  }

  public static function httperf($str) {
    $httperf =  self::dir() . "support/httperf";

    if (isset($str)) {
      $httperf = $httperf . " " . $str;
    }

    $httperf = $httperf . " 2>&1";
    return $httperf;
  }

  public static function get_private($class, $method) {
    $class = new ReflectionClass($class);
    $method = $class->getMethod($method);
    $method->setAccessible(true);
    return $method;
  }
}

