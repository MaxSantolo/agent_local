<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 02/11/2018
 * Time: 12:44
 */

require 'tech/connect.php';

$select_admin = "select cliente_email.*, email_lead.id as id from cliente_email 
                 left join crm.email_lead on email = email_address
                 where fl_amministrazione = 'Y' and email_lead.id is not null";

$admin_array_data = $conn_intranet->query($select_admin);

while ($riga = $admin_array_data->fetch_assoc()) {

    $conn_crm->query("UPDATE leads set status = 'ammin' WHERE id = '".$riga['id']."'");

}