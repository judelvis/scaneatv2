<?php


define('OK', 200);
define('VOID', 201);
define('FORBIDDEN', 403);
define('CONFLICT', 409);

define('SIGNUP_EMPTY', 1001);
define('SIGNUP_PASS_MATCH', 1002);
define('SIGNUP_EXISTS', 1003);

define('LOGIN_EMPTY', 1010);
define('NO_VALIDADO', 1011);
define('LOGIN_ERROR', 1012);
define('LOGIN_OK_HAS_REST', 1013);

define('RESET_PASS_EMPTY', 1020);
define('MAIL_ERROR', 1021);
define('RESET_PASS_INVALID', 1022);
define('ALREADY_VALIDATED', 1023);

define('DATA_EMPTY', 1030);
define('COLUMN_NOT_FOUND', 1031);
define('DB_ERROR', 1032);

define('APIKEY_INVALID', 1040);
define('APIKEY_EMPTY', 1041);

define('REST_ID_EMPTY', 1050);
define('LOCATION_EMPTY_RESTAURANTS', 1051);

define('SERVER_API_UPDATED', 2000);
define('INVALID_API_KEY', 2001);
define('UNKNOWN_PLATFORM', 2002);



?>