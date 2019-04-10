<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 10/10/2018
 * Time: 09:19
 */

require 'class/PHPMailerAutoload.php';

$mail = new PHPMailer();
$mail->CharSet = 'UTF-8';
$mail->IsSMTP();
$mail->Host = "10.20.20.227";
$mail->SMTPAutoTLS = true;
$mail->SMTPAuth = false;
$mail->From = "info@pickcenter.com";
$mail->FromName = "Pick Center sistema notifica";
$mail->headerLine("Content-Type: text/html; charset=UTF-8");
$mail->AddAddress('max@swhub.io','MS');
$mail->AddReplyTo('info@pickcenter.com', 'Informazioni');
$mail->WordWrap = 50;
$mail->IsHTML(true);
$mail->Subject = 'Benvenuto nella rete WiFi di Pick Center';
$mail->AltBody = 'Il messaggio &egrave; in formato HTML';
$mail->Body = '<strong><h3>Ciao</h3></strong>';
$mail->send();