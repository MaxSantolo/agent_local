<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 09/08/2018
 * Time: 12:47
 */

require_once 'tech/connect.php';

$subemail_oprhans_array = $conn->query("SELECT * FROM email_check WHERE id_erp = ''");

print_r($subemail_oprhans_array->num_rows);

if ($subemail_oprhans_array->num_rows > 0) {

    while ($email_orph = $subemail_oprhans_array->fetch_assoc()) {

        print_r($email_orph['email']."<BR>");


    }



}