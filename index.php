<!DOCTYPE html>
<html>
<head>
<title>YATM</title>
</head>
<body>
<?php
  include('./functions.php');
  require 'config.php';
  if (!defined('DB_NAME')) {
   header('Location: install.php');
   exit;
  }
?>
  <p><strong>Y</strong>et <strong>A</strong>nother <strong>T</strong>emperature <strong>M</strong>onitoring</p>
<?
if (!($connection = @mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD))) {
  echo "Ошибка соединения: " . mysql_error();
} else {
  if (!mysql_select_db(DB_NAME, $connection)) {
    echo 'Не удалось выбрать базу yatm: ' . mysql_error();
  } else {
    $query = "SELECT * FROM `stations`";
    if (!$result=mysql_query($query,$connection)) {
      echo 'Не удалось получить записи: ' . mysql_error();
    } else {
?>
      <table border=1>
        <tr>
          <th>Station</th>
          <th>Name</th>
          <th>Value</th>
          <th>Location</th>
        <tr>
<?
      while($row = mysql_fetch_array($result)) {
        $json=yatm_server ($row['host'],$row['port'],'values');
        if ($json) {
        $values=json_decode($json, true);

      foreach ($values as $value) {
        $location=mysql_result(mysql_query("SELECT `location` FROM `probes` WHERE `name`=\"".$value['name']."\"",$connection), 0);
?>
        <tr>
          <td><?=$row['name']?></td>
          <td><a href="detail.php?probe=<?=$value['name']?>"><?=$value['name']?></td>
          <td><?=$value['value']?></td>
          <td><?=$location?></td>
        </tr>
<?
      }

      } else {
        echo "Соединиться не удалось.";
      }
      }
?>
      </table>
<?
      mysql_close($connection);
     
    }
  }
}
?>
<p><a href="#">Add Station</a> <a href="#">Add Probe</a><br>
<a href="#">Download Server</a> <a href="#">Download Server Config</a><br>
<a href="#">Download Client</a></p>
<p><a href="drop.php">DROP!</a></p>
</body>
</html>
