<?php

/*
	FUNCION PARA PROCESAR EL MENSAJE RECIBIDO
*/

function newlog($command) {
	$fh = fopen("log.txt", 'a') or die("can't open file");
	$stringData = json_encode($command).chr(10);
	fwrite($fh, $stringData);
	fclose($fh);
}
function reply_message($replyTo, $text){
	if(strpos($text, "{") === 0){
		$array = json_decode($text, true);
		$type = $array["type"];
		unset($array["type"]);
	} else {
		$array = array("text" => $text);
		$type = "message";
	}
	apiRequest("send".$type, array_merge($array, array('chat_id' => $replyTo['chat']['id'], "reply_to_message_id" =>$replyTo["message_id"])));
}
function get_random_message($sourceFile, $fallback = "Error") {
	$frases=file("lib/$sourceFile.txt");
	if(is_array($frases)) {
		shuffle($frases);
		return $frases[0];
	} else {
		return $fallback;
	}
}

function processMessage($message,$update) {
	$text = $message['text'];
	$nombre_saludo = $message["from"]["first_name"];
	$errMsg = "Algo Salió mal ".$nombre_saludo;

	//ES UN COMANDO O NO
	if(strpos($text, "/") === 0){
		$rawText = substr($text, 1);

		$arrayText = explode(" ", $rawText);
		$command = $arrayText[0];
		
		newlog($command); // ONLY FOR DEBUG
		
		if(strpos($text, "/ilumina")===0){
			reply_message($message, get_random_message('feed', $errMsg));
		}

		if(strpos($text, "/miami")===0){
			reply_message($message, get_random_message('miami', $errMsg));
		}
	} else { // NO ES UN COMANDO
		if(stripos($text, "hola bot") === 0 || stripos($text, "hola") === 0 || stripos($text, "Hola comandante") === 0){
			reply_message($message, "Hola " . $nombre_saludo);
		}else{
			reply_message($message, get_random_message('respuestas', "Algo Salió mal ".$nombre_saludo));
		}
	}
}

?>
