<?php
require_once dirname(__FILE__) . '/Parser.php';

class HTTPerf {
  function __construct($options=array(), $path=null) {

    // check and set parse
    $this->parse = false;
    if (isset($options['parse'])) {
      $this->parse = $options['parse'];
      unset($options['parse']);
    }

    // check and set tee
    $this->tee = false;
    if (isset($options['tee'])) {
      $this->tee = $options['tee'];
      unset($options['tee']);
    }

    // check and set command
    if (isset($options['command'])) {
      if (count($options) != 1) {
        throw new Exception('Option command must not be passed with other options.');
      }

      if (!preg_match('/([a-z\/]*)httperf /', $options['command'])) {
        throw new Exception('Invalid httperf command.');
      }

      $this->command = $options['command'];
      unset($options['command']);
    }

    // validate remaining options
    foreach ($options as $key => $val) {
      if (!array_key_exists($key, self::params())) {
        throw new Exception('"'.$key.'" is an invalid param.');
      }
    }

    // validate and set path
    if (!isset($this->command) && isset($path)) {
      $path = trim($path);

      $this->httperf = (preg_match('/httperf$/', $path)) ? $path : $path . '/httperf';

      if (!is_executable($this->httperf)) {
        throw new Exception($this->httperf . ' not found.');
      }
    }

    // find httperf
    if (!isset($this->command) && !isset($this->httperf)) {
      $this->httperf = trim(shell_exec('which httperf'));

      if (!preg_match('/httperf$/', $this->httperf)) {
        throw new Exception('httperf not found.');
      }
    }

    $this->options = array_merge(self::params(), $options);
  }

  function update_options($opt, $val) {
    $this->options[$opt] = $val;
  }

  function options() {
    $options = array();
    foreach ($this->options as $key => $val) {
      if (isset($val)) {
        if ($key == 'hog') {
          if ($val) {
            array_push($options, '--hog');
          }
          continue;
        }

        if ($key == 'verbose') {
          if ($val) {
            array_push($options, '--verbose');
          }
          continue;
        }

        array_push($options, '--'.$key.'='.$val);
      }
    }
    return join(' ', $options);
  }

  function command() {
    if (isset($this->command)) {
      return $this->command;
    }

    return $this->httperf . ' ' . self::options() . ' 2>&1';
  }

  function run() {
    exec(self::command(), $output, $status);
    if ($status != 0) {
      throw new Exception('httperf exited with  status ' .
                            $status .
                            '\n\nhttperf errors:\n----------\n' .
                            $stderr);
    }

    if (isset($this->parse) && $this->parse) {
      $parser = new Parser();
      return $parser->parse($output);
    }

    return join('\n', $output);
  }

  private function params() {
    return array(
      'add-header'       => null,
      'burst-length'     => null,
      'client'           => null,
      'close-with-reset' => null,
      'debug'            => null,
      'failure-status'   => null,
      'hog'              => null,
      'http-version'     => null,
      'max-connections'  => null,
      'max-piped-calls'  => null,
      'method'           => null,
      'no-host-hdr'      => null,
      'num-calls'        => null,
      'num-conns'        => null,
      'period'           => null,
      'port'             => null,
      'print-reply'      => null,
      'print-request'    => null,
      'rate'             => null,
      'recv-buffer'      => null,
      'retry-on-failure' => null,
      'send-buffer'      => null,
      'server'           => null,
      'server-name'      => null,
      'session-cookies'  => null,
      'ssl'              => null,
      'ssl-ciphers'      => null,
      'ssl-no-reuse'     => null,
      'think-timeout'    => null,
      'timeout'          => null,
      'uri'              => null,
      'verbose'          => null,
      'version'          => null,
      'wlog'             => null,
      'wsess'            => null,
      'wsesslog'         => null,
      'wset'             => null
    );
  }

}
