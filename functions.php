<?php
function yatm_server($host,$port,$msg){
  if (($socket = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) >= 0) {
    if (@socket_connect($socket, $host, $port)) {
      socket_write($socket, $msg, strlen($msg)); //Отправляем серверу сообщение
      $result = socket_read($socket, 1024); //Читаем сообщение от сервера

      socket_write($socket, 'close', strlen('close'));
      if (socket_read($socket, 1024) == 'close') {
        if (isset($socket)) {
          socket_close($socket);
        }
      }
    } else {
      $result=False;
    }
  } else {
    $result=False;
  }
  return $result;
}
function yatm_config() {
include('./config.php');
$connection = @mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
if (!$connection) {
  echo "Ошибка соединения: " . mysql_error();
  return false;
} else {
  if (!mysql_select_db(DB_NAME, $connection)) {
    echo 'Не удалось выбрать базу yatm: ' . mysql_error();
    return false;
  } else {
    $query = "SELECT * FROM `stations`";
    if (!$result=mysql_query($query,$connection)) {
      echo 'Не удалось получить записи: ' . mysql_error();
      return false;
      } else {
        $i=1;
        $config="";
        while($row = mysql_fetch_array($result)) {
          $config.="[Station".$i."]\n";
          $config.="Host=".$row['host']."\n";
          $config.="Port=".$row['port']."\n";
          $config.="Name=".$row['name']."\n";
          $i++;
        }
        mysql_close($connection);
        return $config;
      }
    }
  }
}
?>
