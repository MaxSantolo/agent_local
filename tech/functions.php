<head>
    <title>FUNZIONI</title>

    <style>

        body {
            background-image: url(../images/backdrop.jpg);
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            background-color: #464646;
        }

    </style>
</head>

<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 17/09/2018
 * Time: 09:43
 */
include 'connect.php';

if ($_GET['fx'] == 'delete' and isset($_GET['id_save'])) {

    $id_save = $_GET['id_save'];
    $conn_agent->query("DELETE FROM db_savefiles WHERE id = '".$id_save."'");

    echo '<script type="text/javascript">alert("Cancellazione riuscita"), window.location = "../dashboard_management.php";</script>';
    //header("Location: http://192.168.1.40:85/dashboard_management.php");

}

if ($_GET['fx'] == 'restore' and isset($_GET['id_erp'])) {

    $id_erp = $_GET['id_erp'];
    $content_array = $conn_agent->query("SELECT contents FROM db_savefiles WHERE id_erp='".$id_erp."'")->fetch_assoc();
    $contents = $content_array['contents'];

    //$conn_erp->query("UPDATE user_preferences SET contents = '".$contents."' where assigned_user_id = '".$id_erp."' AND category = 'home'");

    echo '<script type="text/javascript">alert("Ripristino riuscito"), window.location = "../dashboard_management.php";</script>';

}