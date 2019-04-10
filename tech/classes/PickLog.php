<?php
/**
 * Classe che contiene le funzioni comuni necessarie allo scaricamento delle voci di fattura da eSolver
 * dei cedolini da Dom2 e la loro sincronizzazione con il sistema dei punti sul sito.
 * Ogni funzione è esplicitamente commentata in linea ed ha un'intestazione con la descrizione generale.
 */


class PickLog
{

    //converte il risultato di una query in testo
    function sql2Text($result,$emptymessage = 'Nessun risultato') {

        $empty = json_encode($emptymessage);
        $text = '' . PHP_EOL;

        while ($res = $result->fetch_assoc()) {
            $text .= json_encode($res) .PHP_EOL .PHP_EOL;
        }

        if ($text == '') return $empty;
        else return $text;

    }

    //scrive qualcosa in un file
    function write2File($app,$content,$action = '',$user = '',$description='', $origin='', $destination='') {

        $now = (new DateTime("Europe/Rome"))->format('d-m-Y H:i:s');
        $logfile = $_SERVER['DOCUMENT_ROOT']."/logs/" . strtoupper($app). "/" . (new DateTime("Europe/Rome"))->format('Ymd') . "_" . $app . ".txt"; //genero il nome del file
        $text = "
START: {$now}>---------------------------------
APP: {$app}
AZN: {$action}
DSC: {$description}
ORG: {$origin}
DST: {$destination}
USR: {$user}
PTH: {$logfile} 
ACK:
{$content}
-------------------------------------------------------->END
        ";
        file_put_contents($logfile,$text,FILE_APPEND); //se il file esiste aggiunge altrimenti lo crea

    }

    function sendLog($params) {

        $curlSES=curl_init();

        curl_setopt($curlSES,CURLOPT_URL,"http://logs.pickcenter.com/API/create_log.php");
        curl_setopt($curlSES,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curlSES,CURLOPT_HEADER, false);
        curl_setopt($curlSES, CURLOPT_POST, true);
        curl_setopt($curlSES, CURLOPT_POSTFIELDS,$params);
        curl_setopt($curlSES, CURLOPT_CONNECTTIMEOUT,10);
        curl_setopt($curlSES, CURLOPT_TIMEOUT,30);
        $return = curl_exec($curlSES);
        curl_close($curlSES);

        return $return;
    }


}