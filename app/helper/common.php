<?php

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