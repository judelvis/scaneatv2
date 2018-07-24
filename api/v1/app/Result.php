<?php

class Result {
	var $status;
	var $message;
	function setStatus($s) {
		$this->status = $s;
	}
	function getStatus() {
		return $this->status;
	}
	function setMessage($m) {
		$this->message = $m;
	}
	function getMessage() {
		return $this->message;
	}

	public static function newInvalidApiKeyResult() {
		$result = new Result();
		$result->setStatus(INVALID_API_KEY);
		$result->setMessage("API key inválida");
		return $result;
	}
	public static function newUnknownPlatformResult() {
		$result = new Result();
		$result->setStatus(UNKNOWN_PLATFORM);
		$result->setMessage("Plataforma desconocida");
		return $result;
	}

	public static function newUpdateAppResult() {
		$result = new Result();
		$result->setStatus(SERVER_API_UPDATED);
		$result->setMessage("Neceistas actualizar tu dispositivo");
		return $result;
	}
}
?>