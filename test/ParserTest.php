<?php
require_once 'helper.php';

class ParserTest extends UnitTestCase {

  function test_verbose_expression() {
    $method = TestHelper::get_private('Parser', 'verbose_expression');
    $parser = new Parser();

    $verbose_expression = $method->invoke($parser);

    $this->assertPattern($verbose_expression, 'Connection lifetime = 111.11');
  }

  function test_expressions() {
    $method = TestHelper::get_private('Parser', 'expressions');
    $parser = new Parser();

    exec(TestHelper::httperf('--url /foo'), $out, $status);
    $this->assertEqual(0, $status);

    $expressions = $method->invoke($parser);

    foreach ($expressions as $key => $exp) {
      $this->assertNotNull(preg_grep($exp, $out), "$key not matched");
    }
  }

  function test_percentiles() {
    $method = TestHelper::get_private('Parser', 'percentiles');
    $parser = new Parser();

    $percentiles = $method->invoke($parser);

    $this->assertEqual(6, count($percentiles));
    $this->assertEqual(75, $percentiles[0]);
    $this->assertEqual(99, $percentiles[5]);
  }

  function test_calculate_percentiles() {
    $parser = new Parser();

    $method = TestHelper::get_private('Parser', 'percentiles');
    $percentiles = $method->invoke($parser);

    $method = TestHelper::get_private('Parser', 'calculate_percentiles');

    $array = range(1, 100);

    foreach ($percentiles as $percentile) {
      $args = array($percentile, $array);
      $percentiles = $method->invokeArgs($parser, $args);

      $this->assertEqual($percentile, $percentiles);
    }
  }

  function test_parser() {
    $parser = new Parser();

    exec(TestHelper::httperf('--url /foo'), $out, $status);
    $matches = $parser->parse($out);

    $this->assertFalse(isset($matches['connection_times']));
    $this->assertFalse(isset($matches['connection_time_75_pct']));
    $this->assertFalse(isset($matches['connection_time_99_pct']));

    $method = TestHelper::get_private('Parser', 'expressions');
    $parser = new Parser();
    $expressions = $method->invoke($parser);

    foreach ($expressions as $key => $expression) {
      $this->assertTrue(isset($matches[$key]));
    }
  }

  function test_parser_verbose() {
    $parser = new Parser();

    exec(TestHelper::httperf('--verbose --url /foo'), $out, $status);
    $matches = $parser->parse($out);

    // verbose connections times
    $this->assertTrue(isset($matches['connection_times']));
    $this->assertTrue(isset($matches['connection_time_75_pct']));
    $this->assertTrue(isset($matches['connection_time_99_pct']));
    $this->assertEqual(100, count($matches['connection_times']));

    $method = TestHelper::get_private('Parser', 'expressions');
    $parser = new Parser();
    $expressions = $method->invoke($parser);

    foreach ($expressions as $key => $expression) {
      $this->assertTrue(isset($matches[$key]));
    }
  }
}
