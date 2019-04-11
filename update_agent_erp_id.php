<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 06/08/2018
 * Time: 15:41
 *
 * Cron che aggiorna sul database del sito gli ID del CRM delle email attive (iscritte) e non attive (blacklisted)
 * generate dalla componente Web dell'Agent
 *
 * Il sistema effettua il LOG sia a livello di applicazione sia a livello di applicativo ".logs"
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

//aggiorno gli id del CRM sul sito per le email normali
$sqlCheckEmail = "SELECT * FROM email_check";
$email_check_array = $conn->query($sqlCheckEmail);

if ($conn->error) $errormsg = "Impossibile eseguire la query: " . $sqlCheckEmail . " - Errore: " . $conn->error . PHP_EOL;

    else {

     while ($row = $email_check_array->fetch_assoc()) {

        $slqSelectIdCRM = "SELECT id FROM email_lead WHERE email_address = '{$row['email']}'";
        $emails = $conn_crm->query($slqSelectIdCRM);

        if ($conn_crm->error) $errormsg .= "Impossibile eseguire la query: " . $slqSelectIdCRM . " - Errore: " . $conn_crm->error . PHP_EOL;

            else {
            $email_lead = $emails->fetch_assoc();

            if (!empty($email_lead['id'])) {

                $sqlUpdateEmailCheck = "UPDATE email_check SET id_erp = '" . $email_lead['id'] . "' WHERE email = '" . $row['email'] . "'";
                $conn->query($sqlUpdateEmailCheck);

                if ($conn->error) $errormsg .= "Impossibile eseguire la query: " . $slqSelectIdCRM . " - Errore: " . $conn_crm->error . PHP_EOL;
                //else $msg = $sqlUpdateEmailCheck . PHP_EOL;  //log troppo lungo

            }
        }

    }
    }
$msg = "Aggiornate {$email_check_array->num_rows} ID di email iscritte." . PHP_EOL;


//aggiorno gli id del CRM sul sito per le email in blacklist

$sqlBlCheckMail = "SELECT * FROM email_check_blacklisted";
$email_check_bl_array = $conn->query($sqlBlCheckMail);

    if ($conn->error) $errormsg .= "Impossibile eseguire la query: " . $sqlBlCheckMail . " - Errore: " . $conn->error . PHP_EOL;
        else {

            while ($row_bl = $email_check_bl_array->fetch_assoc()) {

                $sqlLeadBl = "SELECT id FROM email_lead WHERE email_address = '".$row_bl['email']."'";
                $emailsBl = $conn_crm->query($sqlLeadBl);

                if ($conn_crm->error) $errormsg .= "Impossibile eseguire la query: " . $sqlLeadBl . " - Errore: " . $conn_crm->error . PHP_EOL;
                else {
                    $email_lead_bl = $emailsBl->fetch_assoc();

                    if (!empty($email_lead_bl['id'])) {

                        $sqlUpdateEmailCheckBl = "UPDATE email_check_blacklisted SET id_erp = '" . $email_lead_bl['id'] . "' WHERE email = '" . $row_bl['email'] . "'";
                        $conn->query($sqlUpdateEmailCheckBl);

                        if ($conn->error) $errormsg .= "Impossibile eseguire la query: " . $sqlUpdateEmailCheckBl . " - Errore: " . $conn->error . PHP_EOL;
                        //else $msg .= $sqlUpdateEmailCheckBl . PHP_EOL;
                    }
                }
            }

        }

$msg .= "Aggiornate {$email_check_bl_array->num_rows} ID di email disiscritte";

//log ed email errore

    if ($errormsg == "") {

        Log::wLog("ID del CRM aggiornati correttamente online per SendinBlue.");
        $plog->sendLog(array("app"=>"AGENT","content"=>$msg,"action"=>"ID_CRM_SENDINBLUE"));

    } else {

        $smail = $mail->sendErrorEmail($errormsg,"AZN: ID_SENDINBLUE");
        Log::wLog($errormsg);
        $plog->sendLog(array("app"=>"AGENT","content"=>$errormsg,"action"=>"ID_CRM_SENDINBLUE"));
    }











