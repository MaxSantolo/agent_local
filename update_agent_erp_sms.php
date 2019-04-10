<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 06/08/2018
 * Time: 15:41
 */

include "tech/connect.php";

//$check_array = $conn->query("SELECT * FROM sms_subs"); //prendo i dati dall'agent

$check_array = $conn_crm->query("SELECT * FROM email_lead_sendinblue");

while ($row = $check_array->fetch_assoc()) {

    //$sms2check = preg_replace("/[^0-9]/","",substr($row['sendinblue'],2,strlen($row['sendinblue'])));

    $sms2check = preg_replace("/[^0-9]/","",substr($row['sendinblue'],0,strlen($row['sendinblue'])));



    $esiste = $conn->query("SELECT * FROM sms_subs WHERE SMS = '39".$sms2check."'");

    if ($esiste->num_rows == 0) {

    $conn->query("INSERT INTO sms_subs (email, nome, cognome, sms, to_add, origine, data_aggiunta) VALUES ('".$row['email_address']."','".$row['first_name']."','".$row['last_name']."','39".$row['sendinblue']."','1','CONTRATTO','".date("Y-m-d")."')");

    } else { $conn->query("UPDATE sms_subs SET to_add = '0' WHERE SMS = '39".$sms2check."'"); }


}












