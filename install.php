<?php
define('OK', '<span style="color: green;">OK</span>');
define('NOK', '<span style="color: red;">Not OK</span>');
?>
<!DOCTYPE html>
<html>
<head>
<title>Installation Script</title>
</head>

<?php
$step = (isset($_GET['step']) && $_GET['step'] != '') ? $_GET['step'] : '';
switch($step){
  case '1':
  step_1();
  break;
  case '2':
  step_2();
  break;
  case '3':
  step_3();
  break;
  case '4':
  step_4();
  break;
  case '5':
  step_5();
  break;
  case '6':
  step_6();
  break;
  default:
  step_1();
}
?>

<body>

<?php
function step_1(){ 
 if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['agree'])){
  header('Location: install.php?step=2');
  exit;
 }
 if($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['agree'])){
  echo "You must agree to the license.";
 }
?>
 <p>Our LICENSE will go here.</p>
 <form action="install.php?step=1" method="post">
 <p>
  I agree to the license
  <input type="checkbox" name="agree" />
 </p>
  <input type="submit" value="Continue" />
 </form>
<?php 
}

function step_2(){
  if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['pre_error'] ==''){
   header('Location: install.php?step=3');
   exit;
  }
  if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['pre_error'] != '')
   echo $_POST['pre_error'];
      
  if (phpversion() < '5.0') {
   $pre_error = 'You need to use PHP5 or above for our site!<br />';
  }

  if (ini_get('session.auto_start')) {
   $pre_error .= 'Our site will not work with session.auto_start enabled!<br />';
  }

  if (!extension_loaded('mysql')) {
   $pre_error .= 'MySQL extension needs to be loaded for our site to work!<br />';
  }

  if (!extension_loaded('gd')) {
   $pre_error .= 'GD extension needs to be loaded for our site to work!<br />';
  }

  if (!is_writable('config.php')) {
   $pre_error .= 'config.php needs to be writable for our site to be installed!';
  }

  ?>
  <table width="100%">
  <tr>
    <th>&nbsp;</th>
    <th>Current</th>
    <th>Needed</th>
    <th>Ressult</th>
  </tr>
  <tr>
   <td>PHP Version:</td>
   <td><?php echo phpversion(); ?></td>
   <td>5.0+</td>
   <td><?php echo (phpversion() >= '5.0') ? OK : NOK; ?></td>
  </tr>
  <tr>
   <td>Session Auto Start:</td>
   <td><?php echo (ini_get('session_auto_start')) ? 'On' : 'Off'; ?></td>
   <td>Off</td>
   <td><?php echo (!ini_get('session_auto_start')) ? OK : NOK; ?></td>
  </tr>
  <tr>
   <td>MySQL:</td>
   <td><?php echo extension_loaded('mysql') ? 'On' : 'Off'; ?></td>
   <td>On</td>
   <td><?php echo extension_loaded('mysql') ? OK : NOK; ?></td>
  </tr>
  <tr>
   <td>GD:</td>
   <td><?php echo extension_loaded('gd') ? 'On' : 'Off'; ?></td>
   <td>On</td>
   <td><?php echo extension_loaded('gd') ? OK : NOK; ?></td>
  </tr>
  <tr>
   <td>config.php</td>
   <td><?php echo is_writable('config.php') ? 'Writable' : 'Unwritable'; ?></td>
   <td>Writable</td>
   <td><?php echo is_writable('config.php') ? OK : NOK; ?></td>
  </tr>
  </table>
  <form action="install.php?step=3" method="post">
   <input type="hidden" name="pre_error" id="pre_error" value="<?php echo $pre_error;?>" />
   <input type="submit" name="continue" value="Continue" />
  </form>
<?php
}

function step_3(){
  if (isset($_POST['submit']) && $_POST['submit']=="Continue") {
    header("Location: install.php?step=4");
  }
  if (isset($_POST['submit']) && ($_POST['submit']=="Install!" or $_POST['submit']=="Test")) {
    $db_host=isset($_POST['db_host'])?$_POST['db_host']:"";
    $db_username=isset($_POST['db_username'])?$_POST['db_username']:"";
    $db_password=isset($_POST['db_password'])?$_POST['db_password']:"";

    if (empty($db_host) || empty($db_username) || empty($db_password)) {
      echo "All fields are required! Please re-enter.<br />";
    } else {
      $connection = @mysql_connect($db_host, $db_username, $db_password);
      if (!$connection) {
        echo "Ошибка соединения: " . mysql_error();
      } else {
      if ($_POST['submit']=="Install!") {
      $file ='db.sql';
      if ($sql = file($file)) {
        $query = '';
        foreach($sql as $line) {
          $tsl = trim($line);
          if (($sql != '') && (substr($tsl, 0, 2) != "--") && (substr($tsl, 0, 1) != '#')) {
            $query .= $line;
            
            if (preg_match('/;\s*$/', $line)) {

              mysql_query($query, $connection);
              $err = mysql_error();
              if (!empty($err))
                break;
              $query = '';
            }
          }
        }
        mysql_close($connection);
      }
      $f=fopen("config.php","w");
      $database_inf="";
      $database_inf.="<?php\n";
      $database_inf.="  define('DB_HOST',     '".$db_host."');\n";
      $database_inf.="  define('DB_NAME',     'yatm');\n";
      $database_inf.="  define('DB_USERNAME', 'yatm');\n";
      $database_inf.="  define('DB_PASSWORD', 'zxcvbnm');\n";
      $database_inf.="?>";
      if (fwrite($f,$database_inf)>0){
        fclose($f);
      }
      header("Location: install.php?step=3");
      }
      if ($_POST['submit']=="Test") {
        $query="SELECT IF(EXISTS (SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'yatm'), 'Yes','No')";
        $result=mysql_query($query, $connection);
        switch(mysql_result($result, 0)){
          case 'Yes':
            $db_exists='<span style="color:green;">Database _yatm_ exists!</span>';
          break;
          case 'No':
            $db_exists='<span style="color:red;">Database _yatm_ does not exists!</span>';
          break;
          default:
            $db_exists='<span style="color:red;">Database _yatm_ does not exists!</span>';
        }
        $query="SELECT IF(EXISTS (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'yatm' AND TABLE_NAME = 'stations'), 'Yes','No')";
        $result=mysql_query($query, $connection);
        switch(mysql_result($result, 0)){
          case 'Yes':
            $tb_stations_exists='<span style="color:green;">Table _stations_ exists!</span>';
          break;
          case 'No':
            $tb_stations_exists='<span style="color:red;">Table _stations_ does not exists!</span>';
          break;
          default:
            $tb_stations_exists='<span style="color:red;">Table _stations_ does not exists!</span>';
        }
        $query="SELECT IF(EXISTS (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'yatm' AND TABLE_NAME = 'probes'), 'Yes','No')";
        $result=mysql_query($query, $connection);
        switch(mysql_result($result, 0)){
          case 'Yes':
            $tb_probes_exists='<span style="color:green;">Table _probes_ exists!</span>';
          break;
          case 'No':
            $tb_probes_exists='<span style="color:red;">Table _probes_ does not exists!</span>';
          break;
          default:
            $tb_probes_exists='<span style="color:red;">Table _probes_ does not exists!</span>';
        }
        $query="SELECT IF(EXISTS (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = 'yatm' AND TABLE_NAME = 'data'), 'Yes','No')";
        $result=mysql_query($query, $connection);
        switch(mysql_result($result, 0)){
          case 'Yes':
            $tb_data_exists='<span style="color:green;">Table _data_ exists!</span>';
          break;
          case 'No':
            $tb_data_exists='<span style="color:red;">Table _data_ does not exists!</span>';
          break;
          default:
            $tb_data_exists='<span style="color:red;">Table _data_ does not exists!</span>';
        }
      }
     }
    }
  }

?>
  <form method="post" action="install.php?step=3">
  <p>
   <input type="text" name="db_host" value='localhost' size="30">
   <label for="db_host">MySQL Host</label>
 </p>
 <p>
   <input type="text" name="db_username" size="30" value="<?php echo isset($db_username)? $db_username : 'root' ; ?>">
   <label for="db_username">MySQL Username</label>
 </p>
 <p>
   <input type="text" name="db_password" size="30" value="<?php echo isset($db_password)? $db_password : 'hjvfyjd' ; ?>">
   <label for="db_password">MySQL Password</label>
  </p>
 <p>
   <input type="submit" name="submit" value="Test">
   <input type="submit" name="submit" value="Install!">
   <input type="submit" name="submit" value="Continue">
  </p>
  </form>
<?php
echo isset($db_exists)? $db_exists : '' ;
echo "<br />";
echo isset($tb_stations_exists)? $tb_stations_exists : '' ;
echo "<br />";
echo isset($tb_probes_exists)? $tb_probes_exists : '' ;
echo "<br />";
echo isset($tb_data_exists)? $tb_data_exists : '' ;
}

function step_4(){
/*if (isset($_POST['submit']) && $_POST['submit']=="Continue") {
  header("Location: install.php?step=5");
}
if (isset($_POST['submit']) && $_POST['submit']=="Add" && isset($_POST['host']) && isset($_POST['port']) && isset($_POST['name']) && !empty($_POST['host']) && !empty($_POST['port']) && !empty($_POST['name'])) {
    $host=$_POST['host'];
    $port=$_POST['port'];
    $name=$_POST['name'];
    include('./config.php');
    $connection = @mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
    if (!$connection) {
      echo "Ошибка соединения: " . mysql_error();
    } else {
      if (!mysql_select_db(DB_NAME, $connection)) {
        echo 'Не удалось выбрать базу yatm: ' . mysql_error();
      } else {
        $query = "INSERT INTO stations (host, port, name) VALUES ('$host', '$port', '$name')";
        if (!mysql_query($query,$connection)) {
          echo 'Не удалось добавить запись: ' . mysql_error();
        } else {
          echo 'Запись добавлена. ';
        }
      }
      mysql_close($connection);
    }
  }*/
?>
  <form method="post" action="install.php?step=5">
  <p>
   <input type="text" name="host" size="30" value="127.0.0.1">
   <label for="host">Host</label>
 </p>
 <p>
   <input type="text" name="port" size="30" value="44441">
   <label for="port">Port</label>
 </p>
 <p>
   <input type="text" name="name" size="30" value="HP">
   <label for="port">Name</label>
 </p>
 <p>
   <input type="submit" name="submit" value="Next">
  </p>
  </form>
<?php 
}

function step_5(){
# Получаем host и port из step4
  if (isset($_POST['submit']) && $_POST['submit']=='Next' && isset($_POST['host']) && !empty($_POST['host']) && isset($_POST['port']) && !empty($_POST['port']) && isset($_POST['name']) && !empty($_POST['name'])) {
    $host=$_POST['host'];
    $port=$_POST['port'];
    $name=$_POST['name'];
    include('./functions.php');
# Запрашиваем probes у station
    if ($json=yatm_server ($host,$port,'probes')){
      $probes=json_decode($json, true);
# Предлагаем редактировать probes Location и Description
?>
<form method="post" action="install.php?step=5">
<input type="hidden" name="host" size="30" value="<?=$host?>">
<input type="hidden" name="port" size="30" value="<?=$port?>">
<input type="hidden" name="name" size="30" value="<?=$name?>">
<table>
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Location</th>
          <th>Command</th>
        </tr>
<?
        $i=0;
        foreach ($probes as $probe) {
?>
        <tr>
          <td><input type="hidden" name="probes[<?=$i?>][name]" size="30" value="<?=$probe['name']?>"><?=$probe['name']?></td>
          <td><input type="text" name="probes[<?=$i?>][description]" size="30" value="<?=$probe['description']?>"></td>
          <td><input type="text" name="probes[<?=$i?>][location]" size="30" value="<?=$probe['location']?>"></td>
          <td><?=$probe['command']?></td>
        </tr>
<?
        $i++;
        }
?>
</table>
<input type="submit" name="submit" value="Save">
</form>
<?


    }
  } elseif (isset($_POST['submit']) && $_POST['submit']=='Save' && isset($_POST['probes'])) {
    $probes=$_POST['probes'];
    $host=$_POST['host'];
    $port=$_POST['port'];
    $name=$_POST['name'];
# Пишем Station в БД
    include('./config.php');
    if (!($connection = @mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD))) {
      echo "Ошибка соединения: " . mysql_error();
    } else {
      if (!mysql_select_db(DB_NAME, $connection)) {
      echo 'Не удалось выбрать базу yatm: ' . mysql_error();
      } else {
        $query = "INSERT INTO stations (host, port, name) VALUES ('$host', '$port', '$name')";
        if (!mysql_query($query,$connection)) {
          echo 'Не удалось добавить запись: ' . mysql_error();
        } else {
          echo 'Запись добавлена. ';
        }
# Пишем Probes в БД
        foreach ($probes as $probe) {
          $query = "INSERT INTO probes (name, location, description, station) VALUES ('".$probe['name']."', '".$probe['location']."', '".$probe['description']."', '".$name."')";
          if (!mysql_query($query,$connection)) {
            echo 'Не удалось добавить запись: ' . mysql_error();
          } else {
            echo 'Запись добавлена. ';
          }
        }
      }
    }
# Спарашиваем добавить ли еще один Station
?>
<form method="post" action="install.php?step=5">
  <p>Add one more station?</p>
<input type="submit" name="submit" value="Yes">
<input type="submit" name="submit" value="No">
</form>
<?
  } elseif (isset($_POST['submit']) && $_POST['submit']=='Yes') {
    header("Location: install.php?step=4");
  } elseif (isset($_POST['submit']) && $_POST['submit']=='No') {
    header("Location: install.php?step=6");
  } else {
    header("Location: install.php?step=4");
  }

}
function step_6(){
# Генерируем конфиг для yatm-server
include('./functions.php');
if ($config=yatm_config()) {
?>
<form method="post" action="./download.php">
  <input type="hidden" name="name" size="30" value="config.ini">
  <input type="hidden" name="type" size="30" value="text/plain">
  <p><textarea rows="10" cols="45" name="content"><?php echo $config; ?></textarea></p>
  <p><input type="submit" name="submit" value="Download"></p>
</form>

<form method="post" action="./">
<input type="submit" name="submit" value="Finish">
</form>
<?
}
}

/*
function step_5(){

}
  include('./config.php');
  include('./functions.php');
  
  $connection = @mysql_connect(DB_HOST, DB_USERNAME, DB_PASSWORD);
  if (!$connection) {
    echo "Ошибка соединения: " . mysql_error();
  } else {
    if (!mysql_select_db(DB_NAME, $connection)) {
      echo 'Не удалось выбрать базу yatm: ' . mysql_error();
    } else {
      if (isset($_POST['submit']) && $_POST['submit']=="Save") {
        #foreach ($probes as $probe) {
        #  $query = "INSERT INTO "
        #}
      } else {
      $query = "SELECT * FROM `stations`";
      if (!$result=mysql_query($query,$connection)) {
        echo 'Не удалось получить записи: ' . mysql_error();
      } else {
        while($row = mysql_fetch_array($result)) {
          $json=yatm_server ($row['host'],$row['port'],'probes');
          var_dump($json);
          $probes=json_decode($json, true);
          print_r($probes);
        }
        mysql_close($connection);
?>
<form method="post" action="install.php?step=6">

<table>
        <tr>
          <th>Station</th>
          <th>Name</th>
          <th>Description</th>
          <th>Location</th>
          <th>Command</th>
        </tr>
<?
        $i=0;
        foreach ($probes as $probe) {
?>
        <tr>
          <td><input type="hidden" name="probes[<?=$i?>][station]" size="30" value="<?=$probe['station']?>"><?=$probe['station']?></td>
          <td><input type="hidden" name="probes[<?=$i?>][name]" size="30" value="<?=$probe['name']?>"><?=$probe['name']?></td>
          <td><input type="text" name="probes[<?=$i?>][description]" size="30" value="<?=$probe['description']?>"></td>
          <td><input type="text" name="probes[<?=$i?>][location]" size="30" value="<?=$probe['location']?>"></td>
          <td><?=$probe['command']?></td>
        </tr>
<?
        $i++;
        }
?>
</table>
<input type="submit" name="submit" value="Save">
</form>
<?
      }
     }
    }
  }
*/
?>
