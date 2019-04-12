<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 06/08/2018
 * Time: 15:41
 */

include "tech/connect.php";
require_once $_SERVER['DOCUMENT_ROOT']."/tech/class/PHPMailerAutoload.php";
require_once $_SERVER['DOCUMENT_ROOT']."/tech/classes/Mail.php";
require_once $_SERVER['DOCUMENT_ROOT']."/tech/classes/Log.php";
require_once $_SERVER['DOCUMENT_ROOT']."/tech/classes/PickLog.php";

$mail = new Mail();
$plog = new PickLog();

$errormsg = "";
$msg = "";

$sqlEmailLeadSB = "SELECT * FROM email_lead_sendinblue";
$check_array = $conn_crm->query($sqlEmailLeadSB);

if ($conn_crm->error) $errormsg = "Impossibile eseguire la query: " . $sqlEmailLeadSB . " - Errore: " . $conn_crm->error . PHP_EOL;
else {

    while ($row = $check_array->fetch_assoc()) {

    $sms2check = preg_replace("/[^0-9]/", "", substr($row['sendinblue'], 0, strlen($row['sendinblue'])));

    $sqlExists = "SELECT * FROM sms_subs WHERE SMS = '39" . $sms2check . "'";
    $esiste = $conn->query($sqlExists);

    if ($conn->error) $errormsg .= "Impossibile eseguire la query: " . $sqlExists . " - Errore: " . $conn->error . PHP_EOL;

    else {

        if ($esiste->num_rows == 0) {

            $now = (new DateTime("Europe/Rome"))->format('Y-m-d');
            $sqlInsertSMSSub = "INSERT INTO sms_subs (email, nome, cognome, sms, to_add, origine, data_aggiunta) VALUES ('" . $row['email_address'] . "','" . $row['first_name'] . "','" . $row['last_name'] . "','39" . $row['sendinblue'] . "','1','CONTRATTO','" . $now . "')";
            $conn->query($sqlInsertSMSSub);

            if ($conn->error) $errormsg .= "Impossibile eseguire la query: " . $sqlInsertSMSSub . " - Errore: " . $conn->error . PHP_EOL;
            // else $msg .= $sqlInsertSMSSub . PHP_EOL;  //log troppo lungo

        } else {
            $sqlUpdateSMSSub = "UPDATE sms_subs SET to_add = '0' WHERE SMS = '39" . $sms2check . "'";
            $conn->query($sqlUpdateSMSSub);

            if ($conn->error) $errormsg .= "Impossibile eseguire la query: " . $sqlUpdateSMSSub . " - Errore: " . $conn->error . PHP_EOL;
            //else $msg .= $sqlUpdateSMSSub . PHP_EOL;  //log troppo lungo
        }
    }
}
}
$msg = "Inseriti / Aggiornati numeri di telefono dal CRM a SendinBlue.";


if ($errormsg == "") {

    Log::wLog("Numeri SMS aggiornati correttamente online per SendinBlue.");
    $plog->sendLog(array("app"=>"AGENT","content"=>$msg,"action"=>"CELLULARI_SENDINBLUE"));

} else {

    $smail = $mail->sendErrorEmail($errormsg,"AZN: CELLULARI_SENDINBLUE");
    Log::wLog($errormsg);
    $plog->sendLog(array("app"=>"AGENT","content"=>$errormsg,"action"=>"CELLULARI_SENDINBLUE"));
}












