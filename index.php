<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set("display_errors", 1);
require("lib/config.php");
require("lib/functions.php");
require("lib/message.php");



$content = file_get_contents("php://input");
$update = json_decode($content, true);

/*$fh = fopen("log-update.txt", 'a') or die("can't open file");
	$stringData = $content.chr(10);
	fwrite($fh, $stringData);
	fclose($fh);*/

if (!$update) {
  // receive wrong update, must not happen
  exit;
}


if (isset($update["message"])) {
  // mensaje recibido, proceso el mensaje  
	
	
  processMessage($update["message"],$update);
}


?>