<?php

session_start();
$captcha = $_SESSION['captcha'];
$fakt = $_POST['captcha'];
session_write_close();

$result = ['success' => false];
$code = $_POST['captcha'];

if (empty($code)) {
  $result['errors'][] = ['captcha', 'Пожалуйста введите код!'];
} else {
  $code = crypt(trim($code), '$1$itchief$7');
  $result['success'] = $captcha === $fakt;
  if (!$result['success']) {
    $result['errors'][] = ['captcha', 'Введенный код не соответствует изображению!'];
  }
}

echo json_encode($result);