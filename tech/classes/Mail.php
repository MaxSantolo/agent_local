<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 05/12/2018
 * Time: 16:38
 */

class Mail extends PHPMailer
{
    public function __construct($exceptions=true)
    {
        $this->frommail = "info@pickcenter.com";
        $this->fromname = "Informazioni Agent CRM";
        $this->CharSet= 'UTF-8';
        $this->Host = 'smtp.gmail.com';
        $this->SMTPAuth = true;
        $this->Port = 587;
        $this->SMTPSecure = 'tls';
        $this->Username = "info@pickcenter.com";
        $this->Password = "fm105pick";
        $this->isSMTP();
        parent::__construct($exceptions);
    }

    public function sendEmail($to,$toname,$subject,$body,$ccarray = NULL) {

        $this->From = $this->frommail;
        $this->FromName = $this->fromname;
        $this->AddAddress($to,$toname);

        if (!is_null($ccarray)) {
            foreach ($ccarray as $cc) {
                $this->AddCC($cc);
            }
        }

        $this->AddReplyTo("info@pickcenter.com", "Informazioni");
        $this->Subject = $subject;
        $this->Body    = $body;
        $this->IsHTML(true);

        $this->AltBody = 'Il messaggio è in formato HTML si prega di attivare questa modalità';
        return $this->send();

    }

    public function sendErrorEmail($body,$subject="Errore AGENT CRM") {

        $this->From = $this->frommail;
        $this->FromName = $this->fromname;
        $this->AddAddress("max@swhub.io", "MS");
        $this->AddCC("cea@pickcenter.com", "LC");
        $this->AddCC("bucci@pickcenter.com", "MB");
        $this->AddCC("roberta@pickcenter.com", "RG");
        $this->AddReplyTo("info@pickcenter.com", "Informazioni");
        $this->Subject = $subject;
        $this->Body    = $body;
        $this->IsHTML(true);

        $this->AltBody = 'Il messaggio è in formato HTML si prega di attivare questa modalità';
        return $this->send();

    }


}