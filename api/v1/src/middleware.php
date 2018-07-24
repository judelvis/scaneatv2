<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);
define('ANDROID_MIN_VERSION', 12);
define('IOS_MIN_VERSION', 1);
define('WEB_MIN_VERSION', 1);
define('API_KEY', "b0d43b92-b407-4e69-8ae7-65db5090a9b0");

$app->add(function ($request, $response, $next) {
    $path = $request->getUri()->getPath();
    if ((strpos($path, 'user/resetpassword') !== false)
    || strpos($path, 'user/validate') !== false) {
        $response = $next($request, $response);
    } else {
        $headers = $request->getHeaders();
        $apiKey = "";
        $isAndroid = 0;
        $isIOS = 0;
        $isWeb = 0;
        $versionNumber = -1;
        $log = "";
        if ($request->hasHeader('apikey')) {
            $apiKey = $request->getHeaderLine('apikey');
        }
        if ($request->hasHeader('android')) {
            $isAndroid = $request->getHeaderLine('android');
        }
        if ($request->hasHeader('ios')) {
            $isIOS = $request->getHeaderLine('ios');
        }
        if ($request->hasHeader('web')) {
            $isWeb = $request->getHeaderLine('web');
        }
        if ($request->hasHeader('versionnumber')) {
            $versionNumber = $request->getHeaderLine('versionnumber');
        }
        
        if ($apiKey == API_KEY) {
            if ($isAndroid == 1 || $isIOS == 1 || ($isWeb == 1)) {
                if (($isWeb == 1 && $versionNumber >= WEB_MIN_VERSION)  
                        || ($isAndroid == 1 && $versionNumber >= ANDROID_MIN_VERSION) 
                        || ($isIOS == 1 && $versionNumber >= IOS_MIN_VERSION)) {
                    $response = $next($request, $response);
                } else {
                    $result = Result::newUpdateAppResult();
                }
            } else {
                $result = Result::newUnknownPlatformResult();
            }
        } else {
            $result = Result::newInvalidApiKeyResult();
        }
        if ($result) {
            $response = $response->withAddedHeader('Content-type', 'application/json');
            $response->getBody()->write(json_encode($result));
        }
    }
	return $response;
});