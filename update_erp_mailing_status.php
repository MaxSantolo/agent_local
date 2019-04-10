<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 06/08/2018
 * Time: 15:41
 */

include "tech/connect.php";

$optin_status_array = $conn->query("SELECT * FROM email_check WHERE id_erp != ''");
while ($row = $optin_status_array->fetch_assoc()) {
    $conn_crm->query("UPDATE leads_cstm SET mailing_status_c = 'optin', mailing_date_c = '".$row['mod_date']."' where id_c = '".$row['id_erp']."'");
}

$optout_status_array = $conn->query("SELECT * FROM email_check_blacklisted WHERE id_erp != ''");
while ($row_bl = $optout_status_array->fetch_assoc()) {
    $conn_crm->query("UPDATE leads_cstm SET mailing_status_c = 'optout', mailing_date_c = '".$row_bl['optout_date']."' where id_c = '".$row_bl['id_erp']."'");
}

