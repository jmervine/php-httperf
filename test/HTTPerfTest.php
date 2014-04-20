<?php
require_once 'helper.php';
class HTTPerfTest extends UnitTestCase {

  function test_init_empty() {
    $httperf = new HTTPerf();

    $this->assertEqual(37 , count($httperf->options));

    $this->assertFalse($httperf->parse);
    $this->assertFalse($httperf->tee);
  }

  function test_init_params() {
    $opts = array(
      'uri'   => 'bar',
      'tee'   => true,
      'parse' => false
    );

    $httperf = new HTTPerf($opts, 'test/support');

    $this->assertEqual(37 , count($httperf->options));

    $this->assertFalse(isset($this->options['tee']));
    $this->assertFalse(isset($this->options['parse']));

    $this->assertFalse($httperf->parse);
    $this->assertTrue($httperf->tee);

    $this->assertEqual('bar', $httperf->options['uri']);
    $this->assertEqual('test/support/httperf' , $httperf->httperf);

    $httperf = new HTTPerf(array('command'=>'httperf --uri /foo'));
    $this->assertEqual('httperf --uri /foo' , $httperf->command);
  }

  function test_command_exception() {
    $this->expectException();

    $opts = array(
      'command' => 'command',
      'uri' => 'uri'
    );

    $httperf = new HTTPerf($opts);
  }

  function test_invalid_command_exception() {
    $this->expectException();

    $opts = array(
      'command' => 'bad command'
    );

    $httperf = new HTTPerf($opts);
  }

  function test_invalid_options_exception() {
    $this->expectException();

    $opts = array(
      'bad' => true
    );

    $httperf = new HTTPerf($opts);
  }

  function test_invalid_path_exception() {
    $this->expectException();

    $opts = array();
    $httperf = new HTTPerf($opts, '/bad');
  }

  function test_update_options() {
    $opts = array(
      'uri'   => 'bar'
    );

    $httperf = new HTTPerf($opts, 'test/support');

    $this->assertEqual('bar', $httperf->options['uri']);

    $httperf->update_options('uri', 'boo');
    $this->assertEqual('boo', $httperf->options['uri']);
  }

  function test_options() {
    $httperf = new HTTPerf();
    $this->assertEqual('', $httperf->options());

    $opts = array(
      'uri'     => '/foo',
      'hog'     => true,
      'verbose' => false
    );
    $httperf = new HTTPerf($opts);
    $this->assertEqual('--hog --uri=/foo', $httperf->options());
  }

  function test_command() {
    $opts = array(
      'uri'     => '/foo',
      'hog'     => true,
      'verbose' => false
    );

    $httperf = new HTTPerf($opts);
    $this->assertPattern('/httperf --hog --uri=\/foo/', $httperf->command());

    $httperf = new HTTPerf(array('command'=>'httperf --uri /foo'));
    $this->assertEqual('httperf --uri /foo', $httperf->command());
  }

  function test_run() {
    $opts = array(
      'server'  => 'www.google.com',
      'hog'     => true,
      'verbose' => false
    );

    $httperf = new HTTPerf($opts, 'test/support/httperf');

    $result = $httperf->run();

    $this->assertPattern('/httperf --hog --server=www.google.com/', $result);
  }

  function test_run_parser() {
    $opts = array(
      'server'  => 'www.google.com',
      'hog'     => true,
      'parse'   => true
    );

    $httperf = new HTTPerf($opts, 'test/support/httperf');
    $result = $httperf->run();

    $this->assertFalse(isset($result['connection_times']));
    $this->assertFalse(isset($result['connection_time_75_pct']));
    $this->assertFalse(isset($result['connection_time_99_pct']));

    $method = TestHelper::get_private('Parser', 'expressions');
    $expressions = $method->invoke(new Parser());

    foreach ($expressions as $key => $expression) {
      $this->assertTrue(isset($result[$key]));
    }
  }

  function test_run_parser_verbose() {
    $opts = array(
      'server'  => 'www.google.com',
      'hog'     => true,
      'verbose' => true,
      'parse'   => true
    );

    $httperf = new HTTPerf($opts, 'test/support/httperf');
    $result = $httperf->run();

    $this->assertTrue(isset($result['connection_times']));
    $this->assertTrue(isset($result['connection_time_75_pct']));
    $this->assertTrue(isset($result['connection_time_99_pct']));
    $this->assertEqual(100, count($result['connection_times']));

    $method = TestHelper::get_private('Parser', 'expressions');
    $expressions = $method->invoke(new Parser());

    foreach ($expressions as $key => $expression) {
      $this->assertTrue(isset($result[$key]));
    }
  }
}
