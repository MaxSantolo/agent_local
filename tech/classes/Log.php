<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 04/12/2018
 * Time: 14:39
 */

class Log
{

    public static function wLog($message,$type = 'Evento') {
        $now = (new DateTime("Europe/Rome"))->format('d-m-Y H:i:s');
        $logfile = $_SERVER['DOCUMENT_ROOT'].'/general.log';
        $user = "Agent System";
        $text = $now."->".$type.": ".$message." - ".$user.PHP_EOL;
        file_put_contents($logfile,$text,FILE_APPEND);
    }



}