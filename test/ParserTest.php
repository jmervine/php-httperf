<?php
require_once "helper.php";

/***
 * Note:
 *
 * In the tests below, I'm calling "new Parser()" to access the private methods
 * for testing. However, in real use, Parser shouldn"t be instantiated. Instead
 * call the parse method like so:
 *
 * Parser::parse($output);
 */

class ParserTest extends UnitTestCase {

  function testVerboseExpression() {
    $method = TestHelper::getPrivate("Parser", "verboseExpression");
    $parser = new Parser();

    $verbose_expression = $method->invoke($parser);

    $this->assertPattern($verbose_expression, "Connection lifetime = 111.11");
  }

  function testExpressions() {
    $method = TestHelper::getPrivate("Parser", "expressions");
    $parser = new Parser();

    exec(TestHelper::httperf("--url /foo"), $out, $status);
    $this->assertEqual(0, $status);

    $expressions = $method->invoke($parser);

    foreach ($expressions as $key => $exp) {
      $this->assertNotNull(preg_grep($exp, $out), "$key not matched");
    }
  }

  function testPercentiles() {
    $method = TestHelper::getPrivate("Parser", "percentiles");
    $parser = new Parser();

    $percentiles = $method->invoke($parser);

    $this->assertEqual(6, count($percentiles));
    $this->assertEqual(75, $percentiles[0]);
    $this->assertEqual(99, $percentiles[5]);
  }

  function testCalculatePercentiles() {
    $parser = new Parser();

    $method = TestHelper::getPrivate("Parser", "percentiles");
    $percentiles = $method->invoke($parser);

    $method = TestHelper::getPrivate("Parser", "calculatePercentiles");

    $array = range(1, 100);

    foreach ($percentiles as $percentile) {
      $args = array($percentile, $array);
      $percentiles = $method->invokeArgs($parser, $args);

      $this->assertEqual($percentile, $percentiles);
    }
  }

  function testParser() {
    $parser = new Parser();

    exec(TestHelper::httperf("--url /foo"), $out, $status);
    $matches = $parser->parse($out);

    $this->assertFalse(isset($matches["connection_times"]));
    $this->assertFalse(isset($matches["connection_time_75_pct"]));
    $this->assertFalse(isset($matches["connection_time_99_pct"]));

    $method = TestHelper::getPrivate("Parser", "expressions");
    $parser = new Parser();
    $expressions = $method->invoke($parser);

    foreach ($expressions as $key => $expression) {
      $this->assertTrue(isset($matches[$key]));
    }
  }

  function testParserVerbose() {
    exec(TestHelper::httperf("--verbose --url /foo"), $out, $status);
    $matches = Parser::parse($out);

    // verbose connections times
    $this->assertTrue(isset($matches["connection_times"]));
    $this->assertTrue(isset($matches["connection_time_75_pct"]));
    $this->assertTrue(isset($matches["connection_time_99_pct"]));
    $this->assertEqual(100, count($matches["connection_times"]));

    $method = TestHelper::getPrivate("Parser", "expressions");
    $parser = new Parser();
    $expressions = $method->invoke($parser);

    foreach ($expressions as $key => $expression) {
      $this->assertTrue(isset($matches[$key]));
    }
  }
}
