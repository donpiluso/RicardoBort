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
	apiRequest("sendMessage", array('chat_id' => $replyTo['chat']['id'], "reply_to_message_id" =>$replyTo["message_id"], "text" => $text));
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

	//ES UN COMANDO O NO
	if(strpos($text, "/") === 0){
		$rawText = substr($text, 1);

		$arrayText = explode(" ", $rawText);
		$command = $arrayText[0];
		
		newlog($command); // ONLY FOR DEBUG
		
		if(strpos($text, "/ilumina")===0){
			reply_message($message, get_random_message('feed', "Algo Salió mal ".$nombre_saludo));
		}

		if(strpos($text, "/miami")===0){
			reply_message($message, "SE DICE MAIAMEEEE MI AMOR!");
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
