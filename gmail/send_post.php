<?php
/**
 * POSTされたメール情報をセッションに格納
 * Google認証ページへリダイレクト
 * 認証済みでアクセストークンが有効な場合は、そのまま送信実行する（fromアドレスが変更になる場合は、再認証）。
 * 
 * 入力値のバリデーションは省略します。
 */

//POST値チェック

require_once '../vendor/autoload.php';

session_start();

$to = $_POST['to'];
$from = $_POST['from'];
$subject = $_POST['subject'];
$body = $_POST['body'];

if( isset($_SESSION['from']) && $_SESSION['from']!=$from && isset($_SESSION['access_token'])){
	unset($_SESSION['access_token']);
}
	
$_SESSION['to'] = $to;
$_SESSION['from'] = $from;
$_SESSION['subject'] = $subject;
$_SESSION['body'] = $body;


$client = new Google\Client();
$client->setAuthConfig('../_myconf/client_secret.json');
$client->addScope(Google\Service\Gmail::GMAIL_SEND);


if (isset($_SESSION['access_token']) && $_SESSION['access_token']) {
	$client->setAccessToken($_SESSION['access_token']);
	if(!$client->isAccessTokenExpired()){
		//認証済み
		
		header('Location: ' . filter_var("send_exec.php", FILTER_SANITIZE_URL));
		return;
		
	}else{
		//認証済みだが、期限切れ　google認証画面へ
		redirectToAuthPage($client,$from);
	}

} else {
	//未認証　google認証画面へ
	redirectToAuthPage($client,$from);
}

///
function redirectToAuthPage($client,$from) {
	//google認証画面へ
	$protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] )?"https":"http";
	
	$client->setRedirectUri("$protocol://" . $_SERVER['HTTP_HOST'] . '/callback/gmail.php');
	$client->setAccessType('online');
	$client->setIncludeGrantedScopes(true);   // incremental auth
	
	//$client->setState($sample_passthrough_value);
	$client->setLoginHint($from);	//初期表示メールアドレス
	//$client->setPrompt('consent');
	
	$auth_url = $client->createAuthUrl();
//	echo $auth_url;
	
	header('Location: ' . filter_var($auth_url, FILTER_SANITIZE_URL));
}