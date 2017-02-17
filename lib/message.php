<?php

/*
	FUNCION PARA PROCESAR EL MENSAJE RECIBIDO
*/
define("GROUP", "-1001005597502"); //supergroup

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

function forwardPhoto($photos, $target){
	$mayor=0;
	foreach ($photos as $sizes) {
		$actual=$sizes["file_size"];
		if($actual > $mayor){
			$photoid=$sizes["file_id"];
			$mayor = $actual;
		}
	}
	apiRequest("sendPhoto",array("chat_id"=>$target, "photo"=>$photoid)); //reenvía la foto al grupo.
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
	//newlog($message);
	$text = $message['text'];
	$nombre_saludo = $message["from"]["first_name"];
	$errMsg = "Algo Salió mal ".$nombre_saludo;

	/*Procesa Los commandos*/
	if(strpos($text, "/") === 0){
		$rawText = substr($text, 1);

		$arrayText = array_filter(explode(" ", $rawText), 'strlen');
		$command = $arrayText[0];
		
		//newlog($command); // ONLY FOR DEBUG
		
		if(strpos($text, "/ilumina")===0){
			reply_message($message, get_random_message('feed', $errMsg));
		}

		if(strpos($text, "/miami")===0){
			reply_message($message, get_random_message('miami', $errMsg));
		}

		if(strpos($text, "/bowling")===0){
			if(count($arrayText) > 1){
				array_shift($arrayText);
				$respuesta="BASTA DE BOWLING A ".strtoupper(implode(" ", $arrayText))." O LES MANDO A MI SEGURIDAD";
			}else{
				$respuesta="BASTA DE BOWLING CHICOS! MAMAAAAAAAAAA!!!";
			}

			reply_message($message, $respuesta);
		}

		if(stripos($text, "/say")===0 && $message["chat"]["type"]=="private"){
			$msg=str_ireplace("/say", "", $text);
			apiRequest("sendMessage", array("chat_id"=>GROUP,"text"=>mb_strtoupper($msg,"UTF-8")));
		}

		if(stripos($text, "/id")===0){
			reply_message($message, $message["chat"]["id"]);
		}
	}else{

	/*Procesa lo que no sea commando de mensaje privado*/

		if($message["chat"]["type"]=="private"){
			
			if(stripos($text, "hola bot") === 0 || stripos($text, "hola") === 0 || stripos($text, "Hola comandante") === 0){
				reply_message($message, "Hola " . $nombre_saludo);
			
			}elseif (isset($message["photo"])) {
				forwardPhoto($message["photo"], GROUP);
			}elseif($text !=""){
				reply_message($message, get_random_message('respuestas', "Algo Salió mal ".$nombre_saludo));
			}else{

			}

		}//Cierra procesamiento de privados.

		if($message["chat"]["type"]=="group" || $message["chat"]["type"]=="supergroup"){
			if(stripos($text, "hola bot") === 0 || $text == "hola" || stripos($text, "Hola comandante") === 0){
			reply_message($message, "Hola " . $nombre_saludo);
		
			}elseif ($message["new_chat_participant"]) {
				reply_message($message, 'HOLA '.strtoupper($message['new_chat_participant']['first_name'])." BIENVENIDO A MAIAMEEEE MI AMOR..\n\nESTE GRUPO ESTA EN LO MAS ALTO PORQUE ESTOY YO CON MI ROLLS ROYCE\nADEMAS SON TODOS UNOS GENIOS Y HACEN LOS MEJORES STICKERS\nSI SOS PERONCHO PODER IRTE A RESISTIR CON AGUANTE A OTRO LADO, PERO NO VENGAS A MAIAMEEEEEEE\n\nY OJO CON LA BILLETERA, HAY UN TUCUMANO ENTRE NOSOTROS Y SIEMPRE ESTA CON SED DE TARJETAS DE CREDITO NUEVAS\n\nOJALÁ LA VIDA TE SONRÍA COMO ME SONRÍE A MI Y PUEDAS DISFRUTAR LA VIDA COMO LO HAGO YO.BESO");
			}elseif($message["left_chat_participant"] && $message["left_chat_participant"]['id'] == $message['from']['id']) {
				reply_message($message, get_random_message('chau', "Algo Salió mal ".$nombre_saludo));
			}elseif(($text !="" && stripos($text, "@Rickybort_bot")!==false) || $message["reply_to_message"]["from"]["username"]=="Rickybort_bot"){
				reply_message($message, get_random_message('respuestas', "Algo Salió mal ".$nombre_saludo));
			}

		} //Cierra procesamiento en grupo.

	}//cierra procesamiento no es un commando.

}// Cierra función processMessage

?>
