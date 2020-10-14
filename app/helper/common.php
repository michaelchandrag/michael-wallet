<?php
use \Firebase\JWT\JWT;

/**
 *	String to SHA1(MD5())
 *
 * 	@param string
 * 	@return string
 */
function convertToSalt ($string) {
	return sha1(md5($string));
}

function dump ($data) {
	echo '<pre>';
	print_r($data);
	die();
}

/**
 * Return base url ('http://localhost:8080')	
 *
 * @link https://stackoverflow.com/questions/6768793/get-the-full-url-in-php
 * @return string
 */
function getBaseUrl () {
	return $actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
}


/**
 *	Create JWT Token
 *
 * @param $user
 * @return string
 */
function createToken ($user) {
	$secretKey = getenv("JWT_KEY");
	$payload = array(
		"iss" => getBaseUrl(),
		"user" => $user
	);

	$jwt = JWT::encode($payload, $secretKey);
	return $jwt;
}

/**
 * Get payload from JWT Token
 *
 * @
 * 
 */
function getJWTPayload ($token, $key = null) {
	try {
		$secretKey = getenv("JWT_KEY");
		$decoded = JWT::decode($token, $secretKey, array('HS256'));
		if (!empty($key)) {
			return $decoded->{$key};
		}
		return $decoded;
	} catch (\Exception $e) {
		return false;
	}
}