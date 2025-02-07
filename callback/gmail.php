<?php
/**
 * googleから認証後にコールバックされる。
 * 承認済みのリダイレクト URIとして、Google Cloud側のクライアントに登録されてる必要がある。
 */

if(isset($_GET['error'])){
	echo("認証されませんでした。".$_GET['error']);
	return;
}

require_once '../vendor/autoload.php';

$code = $_GET['code'];

$client = new Google\Client();
$client->setAuthConfig('../_myconf/client_secret.json');
$client->addScope(Google\Service\Gmail::GMAIL_SEND);
$accessToken = $client->fetchAccessTokenWithAuthCode($code);

session_start();
$_SESSION['access_token'] = $accessToken;
header('Location: ' . filter_var("/gmail/send_exec.php", FILTER_SANITIZE_URL));


