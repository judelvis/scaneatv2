<?php

use Firebase\JWT\JWT;
use Tuupola\Base62;

class Token extends Result {
	var $token;
	function setToken($s) {
		$this->token = $s;
	}
	function getToken() {
		return $this->token;
	}
	static function isApikeyOK($db, $apikey="") {
		/*$sql = "SELECT 1 FROM ".constant('TABLE_API_KEY').' WHERE '.constant('COL_API_KEY_KEY').'="'.$apikey.'"';
		$dbquery = $db->prepare($sql);
		$dbquery->execute();
		$exist = $dbquery->fetchColumn();
		return $exist ? true : false;*/
		return true;
	}
	static function generateToken($db,$request, $apikey){
		$result = new Token();
		try {
			if (isset($apikey)) {
				if(self::isApikeyOK($db, $apikey)){
					$now = new DateTime();
					$future = new DateTime("now +1 day");
					$server = $request->getServerParams();
					$base62 = new Tuupola\Base62;
					$jti = $base62->encode(random_bytes(16));
					$payload = [
						"iat" => $now->getTimeStamp(),
						"exp" => ($future->getTimeStamp()),
						"jti" => $jti,
						"sub" => $server["PHP_AUTH_USER"]
					];
					$secret = constant('API_KEY');
					$token = JWT::encode($payload, $secret);
					$result->setStatus(OK);
					$result->setToken($token);
					$key = $request->getHeader('Apikey');
				} else {
					$result->setStatus(APIKEY_INVALID);
					$result->setMessage("Invalid api key");
				}
			} else {
				$result->setStatus(APIKEY_EMPTY);
				$result->setMessage("Api key not sent");
			}
		} catch (PDOException $e) {
			$result->setStatus(DB_ERROR);
			$result->setMessage("Error: " . $e->getMessage());
		}
		return $result;
	}
}
?>