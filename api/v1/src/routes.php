<?php
// Routes

$app->post('/user/signin', function ($request, $response) {
	$postVars = $request->getParsedBody();
    $name = $postVars["username"];
    $mail = $postVars["mail"];
    $password = $postVars["password"];
        
	$result = User::signIn($this->db,$name,$mail,$password);
	return $this->response->withJson($result);
});

$app->post('/user/update/{id}', function ($request, $response, $args) {
    $postVars = $request->getParsedBody();
    $user_id = $args['id'];
    $result = User::updateUserData($this->db,$user_id,$postVars);
	return $this->response->withJson($result);
});

$app->post('/user/login', function ($request, $response) {
	$postVars = $request->getParsedBody();
	$email = $postVars["email"];
	$pass = $postVars["password"];
	$result = User::login($this->db,$email,$pass);
	return $this->response->withJson($result);
});

$app->post('/user/login/facebook', function ($request, $response) {
	$postVars = $request->getParsedBody();
    $user_name = $postVars["username"];
    $user_mail = $postVars["mail"];
    $facebookId = $postVars["idSocial"];
	$result = User::loginWithGoogleOrFacebook($this->db,"",$facebookId,$user_name, $user_mail);
	return $this->response->withJson($result);
});

$app->post('/user/login/google', function ($request, $response) {
	$postVars = $request->getParsedBody();
    $user_name = $postVars["username"];
    $user_mail = $postVars["mail"];
    $googleId = $postVars["idSocial"];
	$result = User::loginWithGoogleOrFacebook($this->db,$googleId,"",$user_name, $user_mail);
	return $this->response->withJson($result);
});

$app->get('/ingredient', function ($request, $response) {
	return $this->response->withJson(Ingredient::getAllIngredients($this->db));
});

$app->get('/ingredient/{search}', function ($request, $response, $args) {
	return $this->response->withJson(Ingredient::getIngredientsBy($this->db,$args["search"]));
});

$app->get('/reviews/restaurant/{id}', function ($request, $response, $args) {
	return $this->response->withJson(Review::getReviewsRestaurant($this->db,$args["id"]));
});

$app->get('/reviews/plate/{id}', function ($request, $response, $args) {
	return $this->response->withJson(Review::getReviewsPlate($this->db,$args["id"]));
});

$app->get('/reviews/plate/{id}/{start}/{size}', function ($request, $response, $args) {
	return $this->response->withJson(Review::getReviewsPlatePaged($this->db,$args["id"],
    $args["start"],
    $args["size"]));
});

$app->get('/reviews/restaurant/{id}/{start}/{size}', function ($request, $response, $args) {
	return $this->response->withJson(Review::getReviewsRestaurantPaged($this->db,$args["id"],
    $args["start"],
    $args["size"]));
});

$app->post('/reviews/{id}', function ($request, $response, $args) {
    $postVars = $request->getParsedBody();
	$title = $postVars["title"];
	$comment = $postVars["comment"];
	$presentation = $postVars["presentation"];
	$price_quality = $postVars["price_quality"];
	$savour_texture = $postVars["savour_texture"];
	$user_id = $postVars["user_id"];
	$date = $postVars["date"];
	$day_night = $postVars["day_night"];
	return $this->response->withJson(Review::writeReview($this->db,$args["id"],$title,$comment ,$presentation, $price_quality,
	$savour_texture, $user_id, $date , $day_night));
});

$app->post('/upload/images/review/{plate}/{id}', function ($request, $response, $args) {
    $images = $request->getUploadedFiles();
    $directory = "../img/reviews";
	return $this->response->withJson(Images::uploadImages($directory, $args["plate"],$args["id"],$images));
});

$app->post('/upload/images/user/{id}', function ($request, $response, $args) {
    $images = $request->getUploadedFiles();
    $directory = "../img/user";
	return $this->response->withJson(Images::uploadImages($directory, $args["plate"],$args["id"],$images));
});

$app->get('/plates/{id}', function ($request, $response, $args) {
	return $this->response->withJson(Plates::getPlatesById($this->db, $args["id"]));
});

$app->get('/plates/restaurant/{id}', function ($request, $response, $args) {
	return $this->response->withJson(Plates::getPlatesByRestaurantId($this->db, $args["id"]));
});

$app->get('/plates/best/{town}/{start}/{size}', function ($request, $response, $args) {
	return $this->response->withJson(Plates::getBestPlatesByTown($this->db, $args["town"], $args["start"], $args["size"]));
});

$app->get('/restaurant/{id}', function ($request, $response, $args) {
	return $this->response->withJson(Restaurants::getRestaurantById($this->db, $args["id"]));
});

$app->get('/restaurant/best/{town}/{start}/{size}', function ($request, $response, $args) {
	return $this->response->withJson(Restaurants::getBestRestaurantsByTown($this->db, $args["town"], $args["start"], $args["size"]));
});

$app->get('/restaurant/newest/{town}/{start}/{size}', function ($request, $response, $args) {
	return $this->response->withJson(Restaurants::getNewestRestaurantsByTown($this->db, $args["town"], $args["start"], $args["size"]));
});

$app->get('/restaurant/promoted/{town}/{start}/{size}', function ($request, $response, $args) {
	return $this->response->withJson(Restaurants::getPromotedRestaurantsByTown($this->db, $args["town"], $args["start"], $args["size"]));
});

$app->post('/search/{start}/{size}', function ($request, $response, $args) {
    $searchFilter = $request->getParsedBody();
    $result = new Restaurants();
    
    $isRestaurant = $searchFilter["restaurants"];
    $isPlate = $searchFilter["plates"];

    if ($isRestaurant && $isRestaurant == 1) {
        $result = Search::DoSearch($this->db, $searchFilter, false, $args["start"], $args["size"]);
    } else if ($isPlate && $isPlate == 1) {
        $result = Search::DoSearch($this->db, $searchFilter, true, $args["start"], $args["size"]);
    } else {
        $result->setStatus(ERROR_PLATES_OR_RESTAURANTS);
        $result->setMessage("Indica si quieres restaurantes o platos");
    }
	return $this->response->withJson($result);
});
$app->post('/search/name/{text}/{start}/{size}', function ($request, $response, $args) {
    $searchFilter = $request->getParsedBody();
    $result = new Restaurants();
    
    $isRestaurant = $searchFilter["restaurants"];
    $isPlate = $searchFilter["plates"];

    if ($isRestaurant && $isRestaurant == 1) {
        $result = Search::DoSearchText($this->db, $args["text"], $searchFilter, false, $args["start"], $args["size"]);
    } else if ($isPlate && $isPlate == 1) {
        $result = Search::DoSearchText($this->db, $args["text"], $searchFilter, true, $args["start"], $args["size"]);
    } else {
        $result->setStatus(ERROR_PLATES_OR_RESTAURANTS);
        $result->setMessage("Indica si quieres restaurantes o platos");
    }
	return $this->response->withJson($result);
});

$app->post('/search/{start}', function ($request, $response, $args) {
    $searchFilter = $request->getParsedBody();
	$isRestaurant = $searchFilter["restaurants"];
    $isPlate = $searchFilter["plates"];
    if ($isRestaurant && $isRestaurant == 1) {
        $result = Search::DoSearch($this->db, $searchFilter, false, $args["start"]);
    } else if ($isPlate && $isPlate == 1) {
        $result = Search::DoSearch($this->db, $searchFilter, true, $args["start"]);
    } else {
        $result->setStatus(ERROR_PLATES_OR_RESTAURANTS);
        $result->setMessage("Indica si quieres restaurantes o platos");
    }
	return $this->response->withJson($result);
});

$app->post('/search', function ($request, $response) {
    $searchFilter = $request->getParsedBody();
	$isRestaurant = $searchFilter["restaurants"];
    $isPlate = $searchFilter["plates"];
    if ($isRestaurant && $isRestaurant == 1) {
        $result = Search::DoSearch($this->db, $searchFilter, false);
    } else if ($isPlate && $isPlate == 1) {
        $result = Search::DoSearch($this->db, $searchFilter, true);
    } else {
        $result->setStatus(ERROR_PLATES_OR_RESTAURANTS);
        $result->setMessage("Indica si quieres restaurantes o platos");
    }
	return $this->response->withJson($result);
});

$app->post('/user/validate', function ($request, $response, $args) {
    $postVars = $request->getParsedBody();
	$email = $postVars["email"];
    $result = Email::SendValidateMail($this->db, $email);
    return  $this->response->withJson($result);
});

$app->get('/user/validate/{userId}/{valLink}', function ($request, $response, $args) {
    $userId = $args["userId"];
    $valLink = $args["valLink"];
    $result = User::validate($this->db,$userId,$valLink);
	return $this->response->withJson($result);
});

$app->post('/user/resetpass', function ($request, $response, $args) {
    $postVars = $request->getParsedBody();
	$email = $postVars["email"];
    $result = Email::SendResetPassMail($this->db, $email);
    return  $this->response->withJson($result);
});

$app->post('/user/resetpassword/{userId}/{valLink}', function ($request, $response, $args) {
	$postVars = $request->getParsedBody();
    $userId = $args["userId"];
    $valLink = $args["valLink"];
	$newPass = $postVars["pass"];
    $result = User::resetPassword($this->db, $userId, $newPass, $valLink);
	return $this->response->withJson($result);
});

$app->get('/edit/restaurant/{id}', function ($request, $response, $args) {
	return $this->response->withJson(Restaurants::getRestaurantToEditById($this->db, $args["id"]));
});

$app->post('/edit/restaurant', function ($request, $response, $args) {
    $postVars = $request->getParsedBody();
    error_log(print_r($postVars, true));
	return $this->response->withJson(Restaurants::updateRestaurant($this->db, $postVars));
});

function printQuery($query) {
    echo $query->debugDumpParams();        
}

function getReviewsImage($plate_id,$review_id) {
    $image_url = array();
    foreach( glob('img/reviews/'.$plate_id.'_'.$review_id.'*.*') as $file ){
        array_push($image_url, $file);
    }
    return $image_url;
}

function getUserImage($user_id) {
    $image_url = array();
    foreach( glob('img/user/'.$user_id.'.*') as $file ){
        array_push($image_url, $file);
    }
    return $image_url;
}

function getPlateImage($plate_id) {
    $image_url = array();
    foreach( glob('img/reviews/'.$plate_id.'.*') as $file ){
        array_push($image_url, $file);
    }
    return $image_url;
}

function getRestaurantImage($restaurant_id) {
    $image_url = array();
    foreach( glob('img/restaurant/'.$restaurant_id.'.*') as $file ){
        array_push($image_url, $file);
    }
    return $image_url;
}

function url_string($cadena) {
    $no_permitidas= array ("á","é","í","ó","ú","Á","É","Í","Ó","Ú","ñ","Ñ","Ã","Ì","Ò","Ù","Ã™","Ã ","Ã¨","Ã¬","Ã²","Ã¹","ç","Ç","Ã¢","ê","Ã®","Ã´","Ã»","Ã‚","ÃŠ","ÃŽ","Ã”","Ã›","ü","Ã¶","Ã–","Ã¯","Ã¤","«","Ò","Ã","Ã„","Ã‹","è","û");
    $permitidas= array ("a","e","i","o","u","A","E","I","O","U","n","N","A","E","I","O","U","a","e","i","o","u","c","C","a","e","i","o","u","A","E","I","O","U","u","o","O","i","a","e","U","I","A","E","e","u");
    $texto = strtolower(str_replace($no_permitidas, $permitidas ,$cadena));
    
    $controlFunctionUrl = 0;
    while($controlFunctionUrl == 0){
        if(substr($texto, -1) == " "){
            $texto = substr($texto, 0, -1);
        }
        else{
            $controlFunctionUrl++;
        }
    }
    
    $arrayTexto = explode(" ",$texto);
    $resultado = implode("-", $arrayTexto);
    
    return $resultado;
}

function generateUniqueKey() {
    return generateRandomKey(10);
}
function generateRandomKey($length) {
    $set_salt = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $salt= "";
    for($i = 0; $i < $length; $i++)  
    {  
        $salt .= $set_salt[mt_rand(0, 61)];  
    }  
    return $salt;               
}

function crypt_img() {
    return generateRandomKey(22);
}
            
function crypt_blowfish($password_crypt, $digito = 7) {  
    $set_salt = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';  
    $salt = sprintf('$2a$%02d$', $digito);
    $salt .= generateRandomKey(22);
    return crypt($password_crypt, $salt);  
}

function sendEmail($to, $subject, $message, $headers,$altMessage="") {

        $result = false;
        $mail = new PHPMailer(true);    // the true param means it will throw exceptions on errors, which we need to catch
        $mail->IsSMTP();                // telling the class to use SMTP

        try {
            $mail->SMTPDebug  = 0;
            $mail->SMTPAuth   = true;                   // enable SMTP authentication
            $mail->SMTPSecure = "tls";                         // sets the prefix to the server
            $mail->Host       ="smtp.gmail.com";
            $mail->Port       = 587;                     // set the SMTP port for the GMAIL server
            $mail->Username   = "";                  // GMAIL username
            $mail->Password   = "";              // GMAIL password
            $mail->AddAddress($to);                     // Receiver email
            $mail->IsHTML(true);
            $mail->SetFrom("scaneat@scaneat.es", 'ScanEat');           // email sender
            $mail->Subject = $subject;                  // subject of the message
            $mail->MsgHTML($message);                   // message in the email
            $mail->AltBody = $altMessage;
            $mail->addCustomHeader($headers);
            $mail->Send();
            $result = true;
        } catch (phpmailerException $e) {
        	echo $e->getMessage();
            $result = false;
        } catch (Exception $e) {
        	echo $e->getMessage();
            $result = false;
        }
        return $result;
}