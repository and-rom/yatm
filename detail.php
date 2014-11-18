<a href="./">Back</a>
<?php
  include('./functions.php');
  require 'config.php';
if (!($connection = @mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD))) {
  echo "Ошибка соединения: " . mysql_error();
} else {
  if (!mysql_select_db(DB_NAME, $connection)) {
    echo 'Не удалось выбрать базу yatm: ' . mysql_error();
  } else {
    $query = "SELECT * FROM `data` WHERE `probe`=\"".$_GET['probe']."\"";
    if (!$result=mysql_query($query,$connection)) {
      echo 'Не удалось получить записи: ' . mysql_error();
    } else {
?>
      <table border=1>
        <tr>
          <th>Date Time</th>
          <th>Value</th>
        <tr>
<?
       while ($row = mysql_fetch_array($result)) {
?>
        <tr>
          <td><?=$row['time']?></td>
          <td><?=number_format(round($row['value'],2),2,',',' ')?></td>
        </tr>
<?
       }
?>
</table>
<?
       mysql_close($connection);
     
    }
  }
}
?>
