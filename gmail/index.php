<?php
/**
 * @see https://github.com/googleapis/google-api-php-client/blob/main/docs/oauth-web.md
 * 
 */
//初期値
$to = "";
$from = "";
$subject = "";
$body = "";

session_start();

$to = isset($_SESSION['to'])?$_SESSION['to']:$to;
$from = isset($_SESSION['from'])?$_SESSION['from']:$from;
$subject = isset($_SESSION['subject'])?$_SESSION['subject']:$subject;
$body = isset($_SESSION['body'])?$_SESSION['body']:$body;

//protect from XSS
$to = htmlspecialchars($to);
$from = htmlspecialchars($from);
$subject = htmlspecialchars($subject);
$body = htmlspecialchars($body,ENT_NOQUOTES);

?>
<!DOCTYPE html>
<html>
<head>
<title>Gmail API Test</title>
<meta charset="utf-8" />
<link rel="stylesheet" href="/css/simple.css">
</head>
<body>
	<h1>Gmail API Test</h1>

	<form method="post" action="send_post.php">
		<table>
			<tr>
				<th>From:</th><td><input type="email" name="from" required="required" value="<?=$from?>"></td>
			</tr>
			<tr>
				<th>To:</th><td><input type="email" name="to" required="required" value="<?=$to?>"></td>
			</tr>
			<tr>
				<th>Subject:</th><td><input type="text" name="subject" required="required" value="<?=$subject?>"></td>
			</tr>
			<tr>
				<td colspan="2">
					<textarea rows="10" cols="60" name="body"><?=$body?></textarea>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="text-align:center">
					<button type="submit">送信</button>
				</td>
			</tr>
		</table>
	</form>
</body>
</html>