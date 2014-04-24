<?php
require_once "helper.php";
class HTTPerfTest extends UnitTestCase {

  function testInitEmpty() {
    $httperf = new HTTPerf();

    $this->assertEqual(37 , count($httperf->options));

    $this->assertFalse($httperf->parse);
  }

  function testInitParams() {
    $opts = array(
      "uri"   => "bar",
      "parse" => false
    );

    $httperf = new HTTPerf($opts, "test/support");

    $this->assertEqual(37 , count($httperf->options));

    $this->assertFalse(isset($this->options["parse"]));

    $this->assertFalse($httperf->parse);

    $this->assertEqual("bar", $httperf->options["uri"]);
    $this->assertEqual("test/support/httperf" , $httperf->httperf);

    $httperf = new HTTPerf(array("command"=>"httperf --uri /foo"));
    $this->assertEqual("httperf --uri /foo" , $httperf->command);
  }

  function testCommandException() {
    $this->expectException();

    $opts = array(
      "command" => "command",
      "uri" => "uri"
    );

    $httperf = new HTTPerf($opts);
  }

  function testInvalidCommandException() {
    $this->expectException();

    $opts = array(
      "command" => "bad command"
    );

    $httperf = new HTTPerf($opts);
  }

  function testInvalidOptionsException() {
    $this->expectException();

    $opts = array(
      "bad" => true
    );

    $httperf = new HTTPerf($opts);
  }

  function testInvalidPathException() {
    $this->expectException();

    $opts = array();
    $httperf = new HTTPerf($opts, "/bad");
  }

  function testUpdateOptions() {
    $opts = array(
      "uri"   => "bar"
    );

    $httperf = new HTTPerf($opts, "test/support");

    $this->assertEqual("bar", $httperf->options["uri"]);

    $httperf->updateOptions("uri", "boo");
    $this->assertEqual("boo", $httperf->options["uri"]);
  }

  function testOptions() {
    $httperf = new HTTPerf();
    $this->assertEqual("", $httperf->options());

    $opts = array(
      "uri"     => "/foo",
      "hog"     => true,
      "verbose" => false
    );
    $httperf = new HTTPerf($opts);
    $this->assertEqual("--hog --uri=/foo", $httperf->options());
  }

  function testParams() {
    $method = TestHelper::getPrivate("HTTPerf", "params");
    $params = $method->invoke(new HTTPerf());
    $this->assertEqual(37, count($params));
  }

  function testCommand() {
    $opts = array(
      "uri"     => "/foo",
      "hog"     => true,
      "verbose" => false
    );

    $httperf = new HTTPerf($opts);
    $this->assertPattern("/httperf --hog --uri=\/foo/", $httperf->command());

    $httperf = new HTTPerf(array("command"=>"httperf --uri /foo"));
    $this->assertEqual("httperf --uri /foo 2>&1", $httperf->command());
  }

  function testRun() {
    $opts = array(
      "server"  => "www.google.com",
      "hog"     => true,
      "verbose" => false
    );

    $httperf = new HTTPerf($opts);

    $result = $httperf->run();

    $this->assertPattern("/httperf --hog --server=www.google.com/", $result);
  }

  function testRunParser() {
    $opts = array(
      "server"  => "www.google.com",
      "hog"     => true,
      "parse"   => true
    );

    $httperf = new HTTPerf($opts);
    $result = $httperf->run();

    $this->assertFalse(isset($result["connection_times"]));
    $this->assertFalse(isset($result["connection_time_75_pct"]));
    $this->assertFalse(isset($result["connection_time_99_pct"]));

    $method = TestHelper::getPrivate("Parser", "expressions");
    $expressions = $method->invoke(new Parser());

    foreach ($expressions as $key => $expression) {
      $this->assertTrue(isset($result[$key]));
    }
  }

  function testRunParserVerbose() {
    $opts = array(
      "server"  => "www.google.com",
      "hog"     => true,
      "verbose" => true,
      "parse"   => true
    );

    $httperf = new HTTPerf($opts);
    $result = $httperf->run();

    $this->assertTrue(isset($result["connection_times"]));
    $this->assertTrue(isset($result["connection_time_75_pct"]));
    $this->assertTrue(isset($result["connection_time_99_pct"]));
    $this->assertEqual(100, count($result["connection_times"]));

    $method = TestHelper::getPrivate("Parser", "expressions");
    $expressions = $method->invoke(new Parser());

    foreach ($expressions as $key => $expression) {
      $this->assertTrue(isset($result[$key]));
    }
  }
}
