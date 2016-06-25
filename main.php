<?php
/**
* Telegram Bot Napoli Fly
* @author Francesco Piero Paolicelli @piersoft
*/

include("Telegram.php");
include("settings_t.php");

class mainloop{
const MAX_LENGTH = 4096;
function start($telegram,$update)
{

	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");
	$text = $update["message"] ["text"];
	$chat_id = $update["message"] ["chat"]["id"];
	$user_id=$update["message"]["from"]["id"];
	$location=$update["message"]["location"];
	$reply_to_msg=$update["message"]["reply_to_message"];

	$this->shell($telegram,$text,$chat_id,$user_id,$location,$reply_to_msg);
	$db = NULL;

}

//gestisce l'interfaccia utente
 function shell($telegram,$text,$chat_id,$user_id,$location,$reply_to_msg)
{
	date_default_timezone_set('Europe/Rome');
	$today = date("Y-m-d H:i:s");
if (strpos($text,'❕') === false || strpos($text,'🛫') === false || strpos($text,'🛬') === false){
	$text=str_replace("❕ ","",$text);
	$text=str_replace("🛫 ","",$text);
	$text=str_replace("🛬 ","",$text);
}
	if ($text == "/start" || $text == "Informazioni") {
		$img = curl_file_create('logo.png','image/png');
		$contentp = array('chat_id' => $chat_id, 'photo' => $img);
		$telegram->sendPhoto($contentp);
		$reply = "Benvenuto. Questo è un servizio automatico (bot da Robot) di ".NAME.". In qualsiasi momento scrivendo /start ti ripeterò questo messaggio di benvenuto.\nQuesto bot è stato realizzato da @piersoft insieme a Pietro Caccia a titolo didattico. I dati sono della partecipata del Comune di Napoli e gestore dell'Aereoporto di Napoli GE.S.A.C. S.p.A. e secondo il CAD art.52 tali dati sono openbydefault con lic. CC-BY in accordo con linee guida AGID. Il progetto e il codice sorgente sono liberamente riutilizzabili con licenza MIT.";
		$content = array('chat_id' => $chat_id, 'text' => $reply,'disable_web_page_preview'=>true);
		$telegram->sendMessage($content);
		$log=$today. ";new chat started;" .$chat_id. "\n";
		$this->create_keyboard_temp($telegram,$chat_id);

		exit;

}elseif($location!=null)
		{

		//	$this->location_manager($telegram,$user_id,$chat_id,$location);
		//	exit;

		}

		elseif($text == "Arrivi")
		{


				$urlgd  ="https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20%2A%20&key=".GDRIVEKEY."&gid=".GDRIVEGID1;
				sleep (1);
				$inizio=1;
				$homepage ="";
				//$comune="Lecce";

				//echo $urlgd;
				$csv = array_map('str_getcsv',file($urlgd));
				//var_dump($csv[1][0]);
				$count = 0;
				foreach($csv as $data=>$csv1){
					$count = $count+1;
				}

					function decode_entities($text) {

													$text=htmlentities($text, ENT_COMPAT,'ISO-8859-1', true);
												$text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text); #decimal notation
													$text= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  #hex notation
												$text= html_entity_decode($text,ENT_COMPAT,"UTF-8"); #NOTE: UTF-8 does not work!
				return $text;
					}
				for ($i=$inizio;$i<$count;$i++){


					$homepage .="\n";
					$homepage .="Volo N°: ".$csv[$i][0];
					$homepage .=" da ".$csv[$i][1];
					if ($csv[$i][2] != null) $homepage .=" Aereoporto: ".$csv[$i][2];
					if ($csv[$i][4] != null)$homepage .="\nPrevisto alle ".$csv[$i][4];
					if ($csv[$i][5] != null)$homepage .=" e ".strtolower($csv[$i][5])." alle ".$csv[$i][6];
					$homepage .="\n____________\n";


				}
				$chunks = str_split($homepage, self::MAX_LENGTH);
				foreach($chunks as $chunk) {
					$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
					$telegram->sendMessage($content);
						}
						$this->create_keyboard_temp($telegram,$chat_id);
						exit;
		}
		elseif($text == "Partenze")
		{


				$urlgd  ="https://spreadsheets.google.com/tq?tqx=out:csv&tq=SELECT%20%2A%20&key=".GDRIVEKEY."&gid=".GDRIVEGID2;
				sleep (1);
				$inizio=1;
				$homepage ="";
				//$comune="Lecce";

				//echo $urlgd;
				$csv = array_map('str_getcsv',file($urlgd));
				//var_dump($csv[1][0]);
				$count = 0;
				foreach($csv as $data=>$csv1){
					$count = $count+1;
				}

					function decode_entities($text) {

													$text=htmlentities($text, ENT_COMPAT,'ISO-8859-1', true);
												$text= preg_replace('/&#(\d+);/me',"chr(\\1)",$text); #decimal notation
													$text= preg_replace('/&#x([a-f0-9]+);/mei',"chr(0x\\1)",$text);  #hex notation
												$text= html_entity_decode($text,ENT_COMPAT,"UTF-8"); #NOTE: UTF-8 does not work!
				return $text;
					}
				for ($i=$inizio;$i<$count;$i++){


					$homepage .="\n";
					$homepage .="Volo N° ".$csv[$i][0];
					$homepage .=" per ".$csv[$i][1];
					if ($csv[$i][2] != null) $homepage .=" Aereoporto di ".$csv[$i][2];
					if ($csv[$i][4] != null)$homepage .="\nPrevisto alle ".$csv[$i][4];
					if ($csv[$i][5] != null)$homepage .=" e ".strtolower($csv[$i][5])." alle ".$csv[$i][6];
					$homepage .="\n____________\n";


				}
				$chunks = str_split($homepage, self::MAX_LENGTH);
				foreach($chunks as $chunk) {
					$content = array('chat_id' => $chat_id, 'text' => $chunk,'disable_web_page_preview'=>true);
					$telegram->sendMessage($content);
						}
				$this->create_keyboard_temp($telegram,$chat_id);
				exit;
		}



	}

	function create_keyboard_temp($telegram, $chat_id)
	 {
			 $option = array(["🛫 Partenze","🛬 Arrivi"],["❕ Informazioni"]);
			 $keyb = $telegram->buildKeyBoard($option, $onetime=false);
			 $content = array('chat_id' => $chat_id, 'reply_markup' => $keyb, 'text' => "[Fai una Scelta]");
			 $telegram->sendMessage($content);
	 }


}

?>
