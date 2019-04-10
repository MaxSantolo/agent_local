<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 06/08/2018
 * Time: 15:41
 */

include "tech/connect.php";

$email_check_array = $conn->query("SELECT * FROM email_check");
while ($row = $email_check_array->fetch_assoc()) {
    $email_lead = $conn_crm->query("SELECT id FROM email_lead WHERE email_address = '".$row['email']."'")->fetch_assoc();
    $conn->query("UPDATE email_check SET id_erp = '".$email_lead['id']."' WHERE email = '".$row['email']."'");
}

$email_check_bl_array = $conn->query("SELECT * FROM email_check_blacklisted");
while ($row_bl = $email_check_bl_array->fetch_assoc()) {
    $email_lead_bl = $conn_crm->query("SELECT id FROM email_lead WHERE email_address = '".$row_bl['email']."'")->fetch_assoc();
    $conn->query("UPDATE email_check_blacklisted SET id_erp = '".$email_lead_bl['id']."' WHERE email = '".$row_bl['email']."'");
}










