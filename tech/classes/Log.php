<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 04/12/2018
 * Time: 14:39
 */

class Log
{
//scrive file e ritorna il percorso per confronto
    public static function wTodayPinFile($content) {
        $now = date('Y-m-d');
        $actual_filename = $_SERVER['DOCUMENT_ROOT'].'\tech\logs\pinlist\pinlist_'.$now.'.html';
        $filetoday = fopen($actual_filename, 'w');
        fwrite($filetoday, $content);
        fclose($filetoday);

        return $actual_filename;
    }

    public static function compareFiles($file_a, $file_b)    {
        if (md5_file($file_a) == md5_file($file_b)) return true;
        else return false;
    }

    public static function wLog($message,$type = 'Evento') {
        $now = (new DateTime("Europe/Rome"))->format('d-m-Y H:i:s');
        $logfile = $_SERVER['DOCUMENT_ROOT'].'/general.log';
        (isset($_SESSION['user_name'])) ? $user =  $_SESSION['user_name'] : $user = "SISTEMA";
        $text = $now."->".$type.": ".$message." - ".$user.PHP_EOL;
        file_put_contents($logfile,$text,FILE_APPEND);
    }



    public static function pUser($conn,$vbid) {
        $data = $conn->query("SELECT firstname, lastname, company FROM visual_phonebook WHERE id = {$vbid}")->fetch_assoc();
        return $string = $data['firstname'].' '.$data['lastname']. ' | '. $data['company'];
    }

}