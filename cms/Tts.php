<?php
require_once "vendor/autoload.php";

class TTS {

	public function create_tts($filename, $content) {
		$provider = new \duncan3dc\Speaker\Providers\PicottsProvider;
		$tts = new \duncan3dc\Speaker\TextToSpeech($content, $provider);
		if(file_put_contents('sound/'.$filename, $tts->getAudioData())) {
			return true;
		}
		else {
			return false;
		}
	}


}
?>