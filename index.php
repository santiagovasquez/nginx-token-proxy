<?php

date_default_timezone_set('UTC');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

$xUserId = $_SERVER['HTTP_X_USER'] ?? null;
$decodedQueryString = urldecode($_SERVER['QUERY_STRING']);
parse_str($decodedQueryString, $queryParams);
$url = 'http://10.0.0.1:80' . $decodedQueryString;

if ($_SERVER['HTTP_X_VERIFY_WEB'] == 1) {
	$url = rtrim($url, '&');
	initCurlTEST($url, $_SERVER);
} else {
	if (isset($queryParams['token']) && !empty($xUserId)) {
		$xUserId = explode('--', $xUserId)[0];
		if (validateTokenDaily($queryParams['token'], $xUserId)) {
			$url = preg_replace('/\&?token=[^&]*/', '', $url);
			initCurlTEST($url, $_SERVER);
		} else {
			http_response_code(403);
		}
	} else {
		http_response_code(403);
	}
}

function initCurlTEST($url, $server) {
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_USERAGENT, $server['HTTP_USER_AGENT']);

	$headers = ['X-Real-IP: ' . $server['HTTP_X_REAL_IP']];

	if (isset($server['HTTP_X_FORWARDED_FOR']) && !empty($server['HTTP_X_FORWARDED_FOR'])) {
		$headers['X-Forwarded-For'] = $server['HTTP_X_FORWARDED_FOR'];
	}

	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	$response = curl_exec($ch);

	if (curl_errno($ch)) {
		echo 'Error en cURL: ' . curl_error($ch);
		exit;
	}

	$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);

	if ($httpCode == 200) {
		echo $response;
	} else {
		http_response_code(403);
	}
}

function validateTokenDaily($token, $userId) {
	$currentDate = gmdate('Y-m-d-H');
	$previousDate = gmdate('Y-m-d-H', strtotime('-10 minutes'));
	$nextDate = gmdate('Y-m-d-H', strtotime('+10 minutes'));

	$phraseCurrent = "$userId-yourPhraseSecret-$currentDate";
	$phrasePrevious = "$userId-yourPhraseSecret-$previousDate";
	$phraseNext = "$userId-yourPhraseSecret-$nextDate";

	$expectedTokenCurrent = hash('sha256', $phraseCurrent);
	$expectedTokenPrevious = hash('sha256', $phrasePrevious);
	$expectedTokenNext = hash('sha256', $phraseNext);

	return hash_equals($expectedTokenCurrent, $token) || hash_equals($expectedTokenPrevious, $token) || hash_equals($expectedTokenNext, $token);
}

// Author: Santiago Vasquez Olarte

?>

