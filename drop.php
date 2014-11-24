<?php
$con=mysql_connect('localhost', 'root', 'hjvfyjd') or die(mysql_error());
mysql_query('DROP DATABASE yatm',$con);
file_put_contents('./config.php', '');
header("Location: ./");
?>
