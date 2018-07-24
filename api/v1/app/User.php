<?php

class User extends Result{
	var $user;
	
	function setUser($s) {
		$this->user = $s;
	}
	
	function getUser() {
		return $this->user;
    }

	static function signIn($db,$user_name,$user_mail,$user_pass){
		$result = new User();
		if($user_name == '' || $user_mail == '' || $user_pass == ''){
			$result->setStatus(SIGNUP_EMPTY);
			$result->setMessage("Hay algún campo vacío");
		} else {
            /*$sql="SELECT 0 FROM usuarios WHERE nombre='".utf8_decode($user_name).
            "' OR email='".utf8_decode($user_mail)."'";*/
            $sql="SELECT 0 FROM usuarios WHERE email='".utf8_decode($user_mail)."'";
            $query = $db->prepare($sql);
            $query->execute();
            $num_rows_userExist = $query->rowCount();
            if($num_rows_userExist == 0) {
                $password_crypt = utf8_decode($user_pass);
                $password_crypt = crypt_blowfish($password_crypt);
                $val_link = crypt_img();
                $sql="INSERT INTO usuarios (nombre, email, pass, date, no_admitido, val_link, validate, unique_key)
                VALUES (:nombre, :email, :pass, NOW(),1, :val_link, :validate,:unique_key)";
                $validateUser = 0;
                $uniqueKey = generateUniqueKey();
                $query = $db->prepare($sql);
                $query->bindParam(':nombre', utf8_decode($user_name), PDO::PARAM_STR);
                $query->bindParam(':email', utf8_decode($user_mail), PDO::PARAM_STR);
                $query->bindParam(':pass', $password_crypt, PDO::PARAM_STR);
                $query->bindParam(':val_link', $val_link, PDO::PARAM_STR);
                $query->bindParam(':validate', $validateUser, PDO::PARAM_STR);
                $query->bindParam(':unique_key', $uniqueKey, PDO::PARAM_STR);
                $query->execute();
                $userId = $db->lastInsertId();

                Email::SendValidateMail($db, $user_mail);
                $result->setStatus(OK);
                $result->setMessage("Registro realizado");
            } else {
                $result->setStatus(SIGNUP_EXISTS);
                $result->setMessage("Ya existe ese usuario");
            }
            $sql = 'SELECT id, nombre, email, radio, direccion, latitud, longitud, pass, validate, has_rest FROM usuarios WHERE email="'.utf8_decode($user_mail).'"';
            $query = $db->prepare($sql);
            $query->execute();
            $num_rows_user = $query->rowCount();

            if($num_rows_user > 0){
                while($row=$query->fetch()){
                        $user = $row;
                        unset($user["pass"]);
                }
                $result->setUser($user);
            }
			
		}
		return $result;
	}
    
    static function loginWithGoogleOrFacebook($db,$google_id,$facebook_id,$user_name, $user_mail) {
        $result = new User();
		if($user_name == '' || $user_mail == ''){
			$result->setStatus(SIGNUP_EMPTY);
			$result->setMessage("Hay algún campo vacío");
		} else {
            if ($google_id == '' && $facebook_id == ''){
			    $result->setStatus(SIGNUP_EMPTY);
			    $result->setMessage("Hay algún campo vacío");
            } else {
                $sql='SELECT id, nombre, email, radio, direccion, latitud, longitud, pass, validate, has_rest FROM usuarios WHERE 
                email="'.$user_mail.'"';
                $initialQuery = $db->prepare($sql);
                $initialQuery->execute();
                $num_rows_userExist = $initialQuery->rowCount();
                if($num_rows_userExist == 0) {
                    $val_link = crypt_img();
                    if ($google_id != '') {
                        $sql="INSERT INTO usuarios (nombre, email, date, latitud, longitud, no_admitido, val_link, validate, unique_key, google_id)
                        VALUES (:nombre, :email, NOW() ,1,1,1, :val_link, :validate,:unique_key,:google_id)";
                    } else {
                        $sql="INSERT INTO usuarios (nombre, email, date, latitud, longitud, no_admitido, val_link, validate, unique_key, facebook_id)
                        VALUES (:nombre, :email, NOW() ,1,1,1, :val_link, :validate,:unique_key,:facebook_id)";
                    }
                    $validateUser = 1;
                    $uniqueKey = generateUniqueKey();
                    $signInquery = $db->prepare($sql);
                    $signInquery->bindParam(':nombre', utf8_decode($user_name), PDO::PARAM_STR);
                    $signInquery->bindParam(':email', utf8_decode($user_mail), PDO::PARAM_STR);
                    $signInquery->bindParam(':val_link', $val_link, PDO::PARAM_STR);
                    $signInquery->bindParam(':validate', $validateUser, PDO::PARAM_STR);
                    $signInquery->bindParam(':unique_key', $uniqueKey, PDO::PARAM_STR);
                    if ($google_id != '') {
                        $signInquery->bindParam(':google_id', $google_id, PDO::PARAM_STR);
                    } else {
                        $signInquery->bindParam(':facebook_id', $facebook_id, PDO::PARAM_STR);
                    }
                    $signInquery->execute();
                    $id = $db->lastInsertId();
                    $user = array("id"=>$id,
                        "nombre"=>$user_name,
                        "email"=>$user_mail,
                        "radio"=>25,
                        "validate"=>0,
                        "has_rest"=>0
                    );
                    $result->setStatus(OK);
                    $result->setMessage("Registro realizado");
                } else {
                    if ($google_id != '') {
                        $sql = "UPDATE usuarios SET google_id='".$google_id."', validate = 1 where email='".$user_mail."'";
                    } else {
                        $sql = "UPDATE usuarios SET facebook_id='".$facebook_id."', validate = 1 where email='".$user_mail."'";
                    }
                    $updateQuery = $db->prepare($sql);
                    $updateQuery->execute();
                    while($row=$initialQuery->fetch()){
                            $user = $row;
                            unset($user["pass"]);
                            unset($user["google_id"]);
                            unset($user["facebook_id"]);
                            unset($user["validate"]);
                            $user["validate"] = 1;
                    }
                    $result->setStatus(SIGNUP_EXISTS);
                    $result->setMessage("Ya existe ese usuario");
                }
                $user["allergens"] = Allergens::getAllergensByUserId($db, $user["id"]);
                $result->setUser($user);
			}
        }
        return $result;
    }
    
	static function login($db,$email,$pass){
		$result = new User();
		if($email == '' || $pass == ''){
			$result->setStatus(LOGIN_EMPTY);
			$result->setMessage("Email or password is empty");
		} else {
            $password_crypt = utf8_decode($pass);
            $password_crypt = crypt_blowfish($password_crypt); 
			$sql = 'SELECT id, nombre, email, radio, direccion, latitud, longitud, pass, validate, has_rest FROM usuarios WHERE 
            email="'.$email.'"';
			$query = $db->prepare($sql);
			$query->execute();
            $num_rows_user = $query->rowCount();

            if($num_rows_user == 1){
                while($row=$query->fetch()){
                        $validate = utf8_encode($row['validate']);
                        $nombre_id = utf8_encode($row['nombre']);
                        $has_rest = $row['has_rest'];
                        $user_pass = $row['pass'];
                        $user = $row;
                        $user["allergens"] = Allergens::getAllergensByUserId($db, $user["id"]);
                }
                if (crypt($pass, $user_pass) == $user_pass) {
                    unset($user["pass"]);
                    if ($validate == 0) {
                        $result->setStatus(NO_VALIDADO);
                        $result->setMessage("Mail no validado");
                    } else if ($has_rest == 1) {
                        $result->setStatus(LOGIN_OK_HAS_REST);
                        $result->setMessage("Login correcto. Usuario con restaurante");
                    } else{
                        $result->setStatus(OK);
                        $result->setMessage("Login correcto");
                    }
                    $result->setUser($user);
                } else {
                    $result->setStatus(LOGIN_ERROR);
                    $result->setMessage("Usuario y/o contraseña incorrectos");
                }
            }  else {
                $result->setStatus(LOGIN_ERROR);
                $result->setMessage("Usuario y/o contraseña incorrectos");
            }
		}
		return $result;
	}
	
	static function resetPassword($db, $user_id, $user_pass, $val_link){
		$result = new User();
		if($user_id == '' || $val_link == ''){
			$result->setStatus(RESET_PASS_EMPTY);
			$result->setMessage("User id, token or password is empty");
		} else {
			$sql = "SELECT val_link FROM usuarios WHERE id=".$user_id;
			$dbquery = $db->prepare($sql);
			$dbquery->execute();
			$dbResult = $dbquery->fetch();
			if ($dbResult && $dbResult["val_link"] == $val_link){
                    $password_crypt = utf8_decode($user_pass);
                    $password_crypt = crypt_blowfish($password_crypt); 
                    $sql="UPDATE usuarios SET pass = :pass WHERE id = :id";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':id', $user_id, PDO::PARAM_STR);
                    $stmt->bindParam(':pass', $password_crypt, PDO::PARAM_STR);
                    $stmt->execute();
                    $result->setStatus(OK);
                    $result->setMessage("Contraseña cambiada");
			}
			else {
				$result->setStatus(RESET_PASS_INVALID);
				$result->setMessage("Invalid token and/or user id");
			}
		}
		return $result;
	}

	static function updateUserData($db,$user_id,$data){
		$result = new User();
		if($user_id == '' || $data == null || empty($data)) {
			$result->setStatus(DATA_EMPTY);
			$result->setMessage("User id or user data is empty");
		} else {
            $user_info = "";
            $name = $data["name"];
            $mail = $data["mail"];
            $allergens = $data["allergens"];
            $password = $data["password"];
            $address = $data["address"];
            
            if ($name) {
                $urlName = url_string($name);
                $user_info .= 'nombre = "'. $name . '", ';
            }

            if ($mail) {
                $user_info .= 'email = "'. $mail . '", ';
                
            }
            if ($address) {
                $latitude = $address["lat"];
                $longitude = $address["lon"];
                $address = $address["address"];
                $user_info .= 'latitud = '. $latitude . ',
                 longitud = '. $longitude . ',
                  direccion = "'. $address . '", ';
                
            }
            if ($password) {
                $password_crypt = utf8_decode($password);
                $password_crypt = crypt_blowfish($password_crypt); 
                $user_info .= 'pass = "'. $password_crypt . '"';
                
            }

            if ($allergens) {
                $allergens_delete_sql = "delete from usuarioalergenos where id_usuario=".$user_id;
                $allergens_insert_sql = "insert into usuarioalergenos values ";
                foreach ($allergens as $key => $value) {
                    $allergens_insert_sql .= "(".$user_id.",".$key."),";
                }
                $allergens_insert_sql = substr($allergens_insert_sql, 0, -1);
            }

			
            try
            {
                if ($user_info && $user_info != "") {
                    $user_info=rtrim($user_info,", ");
                    $sql = "UPDATE usuarios SET ".$user_info." WHERE id=".$user_id;
                    $dbquery = $db->prepare($sql);
                    $dbquery->execute();
                }
                if ($allergens_insert_sql && $allergens_insert_sql != "") {
                    $dbquery = $db->prepare($allergens_delete_sql);
                    $dbquery->execute();
                    $dbquery = $db->prepare($allergens_insert_sql);
                    $dbquery->execute();
                }
                $result->setStatus(OK);
                $result->setMessage("User data updated");
            }
            catch(PDOException $err)
            {
                $column_name_error = "SQLSTATE[42S22]: Column not found";
                if (substr($err->getMessage(),0,strlen($column_name_error)) === $column_name_error) {
                    $result->setStatus(COLUMN_NOT_FOUND);
                    $result->setMessage("Column not found");
                } else {
                    $result->setStatus(DB_ERROR);
                    $result->setMessage($err->getMessage());
                }
            }
		}
		return $result;
	}

	static function validate($db,$user_id,$val_link){
		$result = new User();
		if($user_id == '' || $val_link == ''){
			$result->setStatus(RESET_PASS_EMPTY);
			$result->setMessage("User id or token is empty");
		} else {
			$sql = "SELECT validate, val_link FROM usuarios WHERE id=".$user_id;
			$dbquery = $db->prepare($sql);
			$dbquery->execute();
			$dbResult = $dbquery->fetch();
			if ($dbResult){
                if ($dbResult["validate"] == 1) {
                    $result->setStatus(ALREADY_VALIDATED);
                    $result->setMessage("Correo ya validado");
                } else if ($dbResult["val_link"] == $val_link) {
                    $password_crypt = crypt_blowfish($password_crypt); 
                    $sql="UPDATE usuarios SET validate = 1 WHERE id = :id";
                    $stmt = $db->prepare($sql);
                    $stmt->bindParam(':id', $user_id, PDO::PARAM_STR);
                    $stmt->execute();
                    $result->setStatus(OK);
                    $result->setMessage("Correo validado");
                } else {
                    $result->setStatus(RESET_PASS_INVALID);
                    $result->setMessage("Invalid token and/or user id");
                }
			}
			else {
				$result->setStatus(RESET_PASS_INVALID);
				$result->setMessage("Invalid token and/or user id");
			}
		}
		return $result;
	}
    
    static function GetResetPassData($db, $user_mail) {
        $user = array();
        $sql="SELECT id, nombre, val_link FROM usuarios WHERE email='".utf8_decode($user_mail)."'";
        $query = $db->prepare($sql);
        $query->execute();
        $num_rows_user = $query->rowCount();
        if($num_rows_user > 0) {
            $user = $query->fetch();
        }
		return $user;
    }
}