<?php
	session_start();
	include("../settings/connect_datebase.php");
	include("../settings/recaptcha_config.php");
	require_once __DIR__ . "/../../recaptcha/autoload.php";
	
	$login = $_POST['login'] ?? '';
	$password = $_POST['password'] ?? '';
	$captchaResponse = $_POST['g-recaptcha-response'] ?? '';
	
	$recaptcha = new \ReCaptcha\ReCaptcha(RECAPTCHA_SECRET_KEY);
	$resp = $recaptcha->verify($captchaResponse, $_SERVER['REMOTE_ADDR'] ?? null);
	if (!$resp->isSuccess()) {
		echo -1;
		exit;
	}
	
	// ищем пользователя
	$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."'");
	$id = -1;
	
	if($user_read = $query_user->fetch_row()) {
		echo $id;
	} else {
		$mysqli->query("INSERT INTO `users`(`login`, `password`, `roll`) VALUES ('".$login."', '".$password."', 0)");
		
		$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");
		$user_new = $query_user->fetch_row();
		$id = $user_new[0];
			
		if($id != -1) $_SESSION['user'] = $id; // запоминаем пользователя
		echo $id;
	}
?>