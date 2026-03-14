<?php
	session_start();
	include("../settings/connect_datebase.php");
	include("../settings/recaptcha_config.php");
	require_once __DIR__ . "/../../recaptcha/autoload.php";
	
	$login = $_POST['login'] ?? '';
	$password = $_POST['password'] ?? '';
	$captchaResponse = $_POST['g-recaptcha-response'] ?? '';
	
	// Шаг 7: проверка reCAPTCHA на сервере (библиотека из методички)
	$recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_SECRET_KEY);
	$resp = $recaptcha->verify($captchaResponse, $_SERVER['REMOTE_ADDR'] ?? null);
	if (!$resp->isSuccess()) {
		echo '';
		exit;
	}
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");
	
	$id = -1;
	while($user_read = $query_user->fetch_row()) {
		$id = $user_read[0];
	}
	
	if($id != -1) {
		$_SESSION['user'] = $id;
	}
	echo md5(md5($id));
?>