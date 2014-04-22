<?php
 /***
  * Running:
  *
  * $ cd examples
  * $ php -S localhost:9000 // if php 5.4+
  *
  * browser:
  *
  * http://localhost:9000/web.php
  *
  */
?>
<html>
  <head>
    <title> HTTPerf.php Web Example </title>
  </head>
  <body>
  <table width="600px">
<?php
require_once '../HTTPerf.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $options = array(
    'server'    => $_POST['server'],
    'rate'      => $_POST['rate'],
    'num-conns' => $_POST['num-conns'],
    'verbose'   => true,
    'hog'       => true,
    'parse'     => true
  );

  if ($_POST['uri'] !== '/' || $_POST['uri'] !== '')
    $options['uri'] = $_POST['uri'];

  $httperf = new HTTPerf($options);
  $results = $httperf->run();
?>
  <tr>
    <td>
      <pre>
<?php print_r($results); ?>
      </pre>
    </td>
  </tr>
<?php
} else {
?>
  <form method="POST" action="web.php">
    <tr>
      <td>Server:</td>
      <td><input type="text" name="server" value="www.example.com" /></td>
    </tr>
    <tr>
      <td>URI:</td>
      <td><input type="text" name="uri" value="/" /></td>
    </tr>
    <tr>
      <td>Rate:</td>
      <td><input type="text" name="rate" value="1" /></td>
    </tr>
    <tr>
      <td>Connections:</td>
      <td><input type="text" name="num-conns" value="10" /></td>
    </tr>
    <tr>
      <td><input type="submit" value="go" /></td>
      <td></td>
    </tr>
  </form>
<?php
}
?>

