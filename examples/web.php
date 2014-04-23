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
  require_once "../HTTPerf.php";
  $options = array(
    "server"    => $_POST["server"],
    "rate"      => $_POST["rate"],
    "num-conns" => $_POST["num-conns"],
    "verbose"   => true,
    "hog"       => true,
    "parse"     => true
  );
  $httperf = new HTTPerf($options);
?>
<html>
  <head>
    <title> HTTPerf.php Web Example </title>
    <link href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css" rel="stylesheet">
    <style>
      body { margin-top: 75px; }
<?php if ($_SERVER["REQUEST_METHOD"] !== "POST") { ?>
      td { padding: 2.5px !important; }
<?php } ?>
      .jumbotron { padding: 10px; }
    </style>
  </head>
  <body>
    <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">HTTPerf.php Web Example</a>
        </div>
      </div>
    </div>

    <div class="container" role="main">
      <div class="jumbotron">

<?php
  if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if ($_POST["uri"] !== "/" || $_POST["uri"] !== "")
      $options["uri"] = $_POST["uri"];
?>
        <div id="progress-group">
          <h3> Running... </h3>
          <br />
          <br />
          <div class="progress progress-striped active">
            <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
              <span class="sr-only">&nbsp;</span>
            </div>
          </div>
        </div>
<?php
    ob_flush();
    $results = $httperf->run();
?>
        <script>
          document.getElementById('progress-group').style.display = 'none';
        </script>

        <h3> Raw Result </h3>
        <code><pre><?php print($httperf->raw); ?></pre></code>

        <h3> Parsed Result </h3>
        <code><pre><?php print_r($results); ?></pre></code>
<?php
  } else {
?>
        <h3> Raw Result </h3>
        <form method="POST" action="web.php">

        <table width="100%" class="input-group">
          <tr>
            <td width="150px">Server:</td>
            <td><input class="form-control" type="text" name="server" value="www.example.com" /></td>
          </tr>
          <tr>
            <td>URI:</td>
            <td><input class="form-control" type="text" name="uri" value="/" /></td>
          </tr>
          <tr>
            <td>Rate:</td>
            <td><input class="form-control" type="text" name="rate" value="1" /></td>
          </tr>
          <tr>
            <td>Connections:</td>
            <td><input class="form-control" type="text" name="num-conns" value="10" /></td>
          </tr>
          <tr>
            <td></td>
            <td align="right"><input class="form-control btn-success" type="submit" value="go" /></td>
          </tr>
        </table>
        </form>
<?php
}
?>

      </div>
    </div>
  </body>
</html>
