<?php

header('Content-type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  exit();
}

const EMAIL_SETTINGS = [
  'addresses' => ['bogdanova.a.sagirov@gmail.com'],
  'from' => ['akvela25@gmail.com', 'SpaceX'],
  'subject' => 'Форма обратной связи',
  'host' => 'smtp.gmail.com',
  'username' => 'akvela25@gmail.com',
  'password' => 'avnjcgoxjdjzmren',
  'port' => '465'
];

function itc_log($message) {
  if (HAS_WRITE_LOG) {
    error_log('Date:  ' . date('d.m.Y h:i:s') . '  |  ' . $message . PHP_EOL, 3, LOG_FILE);
  }
}

$data = [
  'errors' => [],
  'form' => [],
  'logs' => [],
  'result' => 'success',
  'message' => ''
];

if (!empty($_POST['name'])) {
  $data['form']['name'] = htmlspecialchars($_POST['name']);
} else {
  $data['result'] = 'error';
  $data['errors']['name'] = 'Заполните это поле.';
  itc_log('Не заполнено поле name.');
}

if (!empty($_POST['surname'])) {
  $data['form']['surname'] = htmlspecialchars($_POST['surname']);
} else {
  $data['result'] = 'error';
  $data['errors']['surname'] = 'Заполните это поле.';
  itc_log('Не заполнено поле surname.');
}

if (!empty($_POST['email-phone'])) {
  $data['form']['email-phone'] = htmlspecialchars($_POST['email-phone']);
} else {
  $data['result'] = 'error';
  $data['errors']['email-phone'] = 'Заполните это поле.';
  itc_log('Не заполнено поле email-phone.');
}

if (!empty($_POST['message'])) {
  $data['form']['message'] = htmlspecialchars($_POST['message']);
} else {
  $data['result'] = 'error';
  $data['errors']['message'] = 'Заполните это поле.';
  itc_log('Не заполнено поле message.');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

if ($data['result'] == 'success') {
  $template = file_get_contents(dirname(__FILE__) . '/email.tpl');
  $search = ['%subject%', '%name%', '%surname%', '%email-phone%', '%message%'];
  $replace = [EMAIL_SETTINGS['subject'], $data['form']['name'], $data['form']['surname'], $data['form']['email-phone'], $data['form']['message']];
  $body = str_replace($search, $replace, $template);

  $mail = new PHPMailer(true);
  $mail->SMTPDebug = 2;
  $mail->SMTPSecure = 'ssl';
  $mail->Debugoutput = function($str, $level) {
    $file = __DIR__ . '/logs/smtp_' . date('Y-m-d') . '.log';
    file_put_contents($file, gmdate('Y-m-d H:i:s'). "\t$level\t$str\n", FILE_APPEND | LOCK_EX);
  };
  try {
    $mail->isSMTP();
    $mail->Host = EMAIL_SETTINGS['host'];
    $mail->SMTPAuth = true;
    $mail->Username = EMAIL_SETTINGS['username'];
    $mail->Password = EMAIL_SETTINGS['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = EMAIL_SETTINGS['port'];
    $mail->setLanguage('ru', 'phpmailer/language');
    $mail->setFrom(EMAIL_SETTINGS['from'][0], EMAIL_SETTINGS['from'][1]);
    foreach (EMAIL_SETTINGS['addresses'] as $address) {
      $mail->addAddress(trim($address));
    }
    $mail->CharSet = 'UTF-8';
    $mail->Encoding = 'base64';
    $mail->isHTML(true);
    $mail->Subject = EMAIL_SETTINGS['subject'];
    $mail->Body = $body;
    $mail->send();
    itc_log('Форма успешно отправлена.');
    $data['message'] = 'Данные отправлены';
  } catch (Exception $e) {
    $data['result'] = 'error';
    $data['message'] = 'Ошибка';
    itc_log('Ошибка при отправке письма: ' . $mail->ErrorInfo);
  }
}

echo json_encode($data);
exit();

?>