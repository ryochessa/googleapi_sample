<?php
/**
 * メール送信実行
 * 
 * （前提）
 * Googleb認証済みで、有効なアクセストークンがセッションにセットされている。
 * 
 */
session_start();

//セッションから必要なデータ取り出す
$to = $_SESSION['to'];
$from = $_SESSION['from'];
$subject = $_SESSION['subject'];
$body = $_SESSION['body'];

$access_token = $_SESSION['access_token'];

if(!$to || !$from || !$subject || !$access_token){
	echo "不正な入力値";
	return;
}


require_once '../vendor/autoload.php';

$client = new Google\Client();
$client->setAuthConfig('../_myconf/client_secret.json');
$client->addScope(Google\Service\Gmail::GMAIL_SEND);
$client->setAccessToken($access_token);



$raw = "From: <{$from}>\r\n";
$raw .= "To: <{$to}>\r\n";
$raw .= 'Subject: =?utf-8?B?' . base64_encode($subject) . "?=\r\n";
$raw .= "MIME-Version: 1.0\r\n";
$raw .= "Content-Type: text/html; charset=utf-8\r\n";
$raw .= 'Content-Transfer-Encoding: quoted-printable' . "\r\n\r\n";
$raw .= "{$body}\r\n";
$rawMessage = strtr(base64_encode($raw), array('+' => '-', '/' => '_'));

$message = new Google\Service\Gmail\Message();
$message->setRaw($rawMessage);

$err = "";
try{
	$gmail = new Google\Service\Gmail($client);
	$gmail->users_messages->send("me", $message);
	//トークンと、送信元以外jクリア
	unset($_SESSION['to']);
	unset($_SESSION['subject']);
	unset($_SESSION['body']);
}catch(Exception $e){
	$err = $e->getMessage();
}

$disp = $err?"メールの送信に失敗しました":"メール送信完了";
?>
<!DOCTYPE html>
<html>
<head>
<title>Gmail API Test</title>
<meta charset="utf-8" />
<link rel="stylesheet" href="/css/simple.css">
</head>
<body>
	<div><?=$disp ?></div>
	<?php if($err){?>
	<div>
		<?=$err?>
	</div>
	<?php }?>
	<a href="index.php">別のメールを作成</a>
</body>
</html>