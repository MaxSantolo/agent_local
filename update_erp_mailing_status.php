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

//aggiorno iscritti da SendinBlue
$sqlEmailsCRMID = "SELECT * FROM email_check WHERE id_erp != ''";
$optin_status_array = $conn->query($sqlEmailsCRMID);

if ($conn->error) $errormsg = "Impossibile eseguire la query: " . $sqlEmailsCRMID . " - Errore: " . $conn->error . PHP_EOL;
else {
    while ($row = $optin_status_array->fetch_assoc()) {
        $sqlUpdateMailingStatus = "UPDATE leads_cstm SET mailing_status_c = 'optin', mailing_date_c = '" . $row['mod_date'] . "' where id_c = '" . $row['id_erp'] . "'";
        $conn_crm->query($sqlUpdateMailingStatus);

        if ($conn_crm->error) $errormsg .= "Impossibile eseguire la query: " . $sqlUpdateMailingStatus . " - Errore: " . $conn_crm->error . PHP_EOL;
        // else $msg .= $sqlUpdateMailingStatus . PHP_EOL; //log troppo lungo
    }
}

$msg = "Aggiornato lo stato e la data di iscrizione alla newsletter di {$optin_status_array->num_rows} contatti/clienti.";

//aggiorno Disiscritti
$sqlEmailCRMIDBlacklisted = "SELECT * FROM email_check_blacklisted WHERE id_erp != ''";
$optout_status_array = $conn->query($sqlEmailCRMIDBlacklisted);

if ($conn->error) $errormsg .= "Impossibile eseguire la query: " . $sqlEmailCRMIDBlacklisted . " - Errore: " . $conn->error . PHP_EOL;
else {
    while ($row_bl = $optout_status_array->fetch_assoc()) {

        $sqlUpdateMailingStatusBlacklisted = "UPDATE leads_cstm SET mailing_status_c = 'optout', mailing_date_c = '" . $row_bl['optout_date'] . "' where id_c = '" . $row_bl['id_erp'] . "'";
        $conn_crm->query($sqlUpdateMailingStatusBlacklisted);

        if ($conn_crm->error) $errormsg .= "Impossibile eseguire la query: " . $sqlUpdateMailingStatusBlacklisted . " - Errore: " . $conn_crm->error . PHP_EOL;
        // else $msg .= $sqlUpdateMailingStatusBlacklisted . PHP_EOL;  //log troppo lungo
    }
}

$msg = "Aggiornato lo stato e la data di disiscrizione alla newsletter di {$optout_status_array->num_rows} contatti/clienti.";

//log vari e email errore
if ($errormsg == "") {

    Log::wLog("Numeri SMS aggiornati correttamente online per SendinBlue.");
    $plog->sendLog(array("app"=>"AGENT","content"=>$msg,"action"=>"NEWSLETTER_SENDINBLUE"));

} else {

    $smail = $mail->sendErrorEmail($errormsg,"AZN: NEWSLETTER_SENDINBLUE");
    Log::wLog($errormsg);
    $plog->sendLog(array("app"=>"AGENT","content"=>$errormsg,"action"=>"NEWSLETTER_SENDINBLUE"));
}
