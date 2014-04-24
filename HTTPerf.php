<?php
/**
 * HTTPerf
 *
 * @author Joshua P. Mervine <joshua@mervine.net>
 * @version 1.0.0
 *
 * Primary class for HTTPerf.php.
 *
 *  require_once('HTTPerf.php');
 *  $httperf = new HTTPerf($options);
 *  echo $httperf->run();
 *
 *  $httperf = new HTTPerf($options);
 *  $httperf->parse = true;
 *  print_r($httperf->run());
 *
 */

require_once dirname(__FILE__) . "/Parser.php";

/**
 * HTTPerf class to be instantiated.
 */
class HTTPerf {
  /**
   * Constructor
   *
   * @param mixed[] $options Array of options.
   * @param string $path Path to custom 'httperf' install.
   *
   * @throws Exception if $options['command'] and other $options are passed together.
   * @throws Exception if $options['command'] does not contain 'httperf'.
   * @throws Exception if $options[] key is not a valid httperf argument.
   * @throws Exception if 'httperf' binary is not found.
   * @throws Exception if found 'httperf' binary is not executable.
   */
  public function __construct($options=array(), $path=null) {

    /* check and set parse */
    $this->parse = false;
    if (isset($options["parse"])) {
      $this->parse = $options["parse"];
      unset($options["parse"]);
    }

    /* check and set command */
    if (isset($options["command"])) {
      if (count($options) !== 1)
        throw new Exception("Option command must not be passed with other options.");

      if (!preg_match("/([a-z\/]*)httperf /", $options["command"]))
        throw new Exception("Invalid httperf command.");

      $this->command = $options["command"];
      unset($options["command"]);
    }

    /* validate remaining options */
    foreach ($options as $key => $val) {
      if (!array_key_exists($key, self::params()))
        throw new Exception("\"".$key."\" is an invalid param.");
    }

    /* validate and set path */
    if (!isset($this->command) && isset($path)) {
      $path = trim($path);

      $this->httperf = (preg_match("/httperf$/", $path)) ? $path : $path . "/httperf";

      if (!is_executable($this->httperf))
        throw new Exception($this->httperf . " not found.");
    }

    /* find httperf */
    if (!isset($this->command) && !isset($this->httperf)) {
      $this->httperf = trim(shell_exec("which httperf"));

      if (!preg_match("/httperf$/", $this->httperf))
        throw new Exception("httperf not found.");
    }

    $this->options = array_merge(self::params(), $options);
  }

  /**
   * Update an option after instantiation.
   *
   * @param string $option Option key name.
   * @param string $value Option key value.
   */
  public function updateOptions($option, $value) {
    $this->options[$option] = $value;
  }

  /**
   * Return options array as stringified command arguments.
   *
   * @return string
   */
  public function options() {
    $options = array();
    foreach ($this->options as $key => $val) {
      if (isset($val)) {
        if ($key === "hog") {
          if ($val)
            array_push($options, "--hog");
          continue;
        }

        if ($key === "verbose") {
          if ($val)
            array_push($options, "--verbose");
          continue;
        }

        array_push($options, "--".$key."=".$val);
      }
    }
    return join(" ", $options);
  }

  /**
   * Return final httperf command to be executed.
   *
   * @return string
   */
  public function command() {
    $command = (isset($this->command)) ? $this->command : ($this->httperf . " " . $this->options());

    return $command . " 2>&1";
  }

  /**
   * Run configured httperf command.
   *
   * @return string || mixed[] If 'parse' is true, a mixed[] containing parsed
   * results will be returned. Otherwise, a string containing the raw results
   * will be returned.
   */
  public function run() {
    exec($this->command(), $output, $status);

    if ($status !== 0)
      throw new Exception("httperf exited with  status " .
                            $status .
                            "\n\nhttperf errors:\n----------\n" .
                            $stderr);

    $this->raw = join("\n", $output);

    if (isset($this->parse) && $this->parse)
      return Parser::parse($output);

    return $this->raw;
  }

  /**
   * httperf arguement definition
   *
   * @return string[]
   */
  private static function params() {
    return array(
      "add-header"       => null,
      "burst-length"     => null,
      "client"           => null,
      "close-with-reset" => null,
      "debug"            => null,
      "failure-status"   => null,
      "hog"              => null,
      "http-version"     => null,
      "max-connections"  => null,
      "max-piped-calls"  => null,
      "method"           => null,
      "no-host-hdr"      => null,
      "num-calls"        => null,
      "num-conns"        => null,
      "period"           => null,
      "port"             => null,
      "print-reply"      => null,
      "print-request"    => null,
      "rate"             => null,
      "recv-buffer"      => null,
      "retry-on-failure" => null,
      "send-buffer"      => null,
      "server"           => null,
      "server-name"      => null,
      "session-cookies"  => null,
      "ssl"              => null,
      "ssl-ciphers"      => null,
      "ssl-no-reuse"     => null,
      "think-timeout"    => null,
      "timeout"          => null,
      "uri"              => null,
      "verbose"          => null,
      "version"          => null,
      "wlog"             => null,
      "wsess"            => null,
      "wsesslog"         => null,
      "wset"             => null
    );
  }
}
