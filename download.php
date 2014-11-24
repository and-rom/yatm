<?php
  header("Content-type: ".$_POST['type']);
  header("Content-Disposition: attachment;filename=".$_POST['name']);
  echo $_POST['content'];
?>
