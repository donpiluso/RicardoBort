<?php

/*
	FUNCION PARA PROCESAR EL MENSAJE RECIBIDO
*/
function processMessage($message,$update) {
	$message_id = $message['message_id'];
	$chat_id = $message['chat']['id'];
	$text = $message['text'];
	$nombre_saludo = $message["from"]["first_name"];
	 

	//ES UN COMANDO O NO
	if(strpos($text, "/") === 0){
		$rawText = substr($text, 1);

		$arrayText = explode(" ", $rawText);
		$command = $arrayText[0];
		/* ONLY FOR DEBUG*/
		
	  		$fh = fopen("log.txt", 'a') or die("can't open file");
			$stringData = json_encode(array_search($command)).chr(10);
			fwrite($fh, $stringData);
			fclose($fh);
		if(strpos($text, "/ilumina")===0){
			$frases=file("lib/feed.txt");
			if(is_array($frases)){
				shuffle($frases);
				 apiRequest("sendMessage", array('chat_id' => $chat_id,"reply_to_message_id" =>$message["message_id"], "text" => $frases[0]));
			}else{
				 apiRequest("sendMessage", array('chat_id' => $chat_id,"reply_to_message_id" =>$message["message_id"], "text" => "Algo Salió mal ".$nombre_saludo));
			}

		}

		if(strpos($text, "/miami")===0){
			apiRequest("sendMessage", array('chat_id' => $chat_id,"reply_to_message_id" =>$message["message_id"], "text" => "SE DICE MIAMEEEE MI AMOR!"));
			
		}

	  	
		

	}else{ // NO ES UN COMANDO

		
		if(strpos($text, "hola bot") === 0 || strpos($text, "Hola bot") === 0 || strpos($text, "hola") === 0 || strpos($text, "Hola") === 0 || strpos($text, "Hola comandante") === 0){
			
			//ALGUIEN SALUDA, EL BOT SALUDA	
			 apiRequest("sendMessage", array('chat_id' => $chat_id,"reply_to_message_id" =>$message["message_id"], "text" => "Hola ".$nombre_saludo));
		}
	}
		
	
}


?>