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
        $ini = parse_ini_file($_SERVER['DOCUMENT_ROOT'].'/logs.ini',true);
        $this->rulebookUrl = $ini['Punti']['Regolamento'];
        $this->newsletterPoints = $ini['Punti']['Newsletter'];
        $this->tomail = $ini['Email']['NotificaA'];
        $this->toname = $ini['Email']['NomeNotificaA'];
        $this->copies = array($ini['Email']['NotificaCC1'],$ini['Email']['NotificaCC2'],$ini['Email']['NotificaCC3'],$ini['Email']['NotificaCC4']);
        $this->frommail = $ini['Email']['From'];
        $this->fromname = $ini['Email']['FromName'];
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

    //manda la mail con gli errori di codice fiscale / partita iva
    public function sendAccountsErrors() {
        $db = new DB();
        $conn = $db->getProdConn('crm_punti');
        $body = "<P>Controllare i seguenti clienti sul CRM (mancanti o con codice fiscale sbagliato/mancante):</P> <table style='width: auto;border:1px solid;border-collapse: collapse'><thead style='border:1px solid;border-collapse: collapse'><th>Nome</th><th>Codice Fiscale</th><th>Partita IVA</th></thead><tbody >";
        $datas = $conn->query("SELECT * FROM users WHERE status = 'nocf' ORDER BY company");
        while ($data = $datas->fetch_assoc()) {
            $body .= "<tr style='border:1px solid;border-collapse: collapse'><td>{$data['company']}</td><td>{$data['codfiscale']}</td><td>{$data['partitaiva']}</td></tr>";
        }
        $body .= "</tbody></table>";
        $mail = new Mail();
        $mail->sendEmail($this->tomail,$this->toname,$this->frommail,$this->fromname,'Errori Accout',$body,$this->copies);
    }

    //for testing
    public function testValues() {
        echo $this->tomail . ' | ' . $this->toname. ' | ' .$this->frommail. ' | ' .$this->frommail . ' | '. $this->copies;
    }

    //email header + footer con variabile per il corpo del testo
    public function mailHeaderFooter($body) {
        $mailbody = "
            <div class=\"mozaik-inner\" style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px 30px; margin: 0px auto; width: 800px; background-color: #fafafa;\">
            <div id=\"sugar_text_:2hn\" style=\"font-family: arial, sans-serif; font-size: 16px; line-height: 25.6px; color: #444444; padding: 0px; margin: 0px;\">&nbsp;</div>
            <div id=\"sugar_text_:2iv\" class=\"ii gt\" style=\"font-size: 12.8px; margin: 5px 15px 0px 0px; padding: 0px 0px 5px; font-family: arial, sans-serif; line-height: 20.48px; color: #444444;\">
            <div id=\"sugar_text_:2gx\" class=\"a3s aXjCH m1615230459ff5a71\" style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\">
            <div dir=\"ltr\" style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\">
            <div class=\"gmail_default\" style=\"font-family: verdana, sans-serif; font-size: 13px; color: #674ea7; line-height: 20.8px; padding: 0px; margin: 0px;\">
            <div dir=\"ltr\" style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\">
            <div class=\"gmail_default\" style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\">
            <div dir=\"ltr\" style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\">
            <div class=\"gmail_default\" style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\">
            <div class=\"gmail_default\" style=\"color: #222222; font-size: 12.8px; font-family: Arial, Helvetica, sans-serif; line-height: 20.48px; padding: 0px; margin: 0px;\">
                <table style=\"color: #000000; font-size: 16px; padding: 10px; margin: 0px; width: 748px; text-align: center; font-family: Verdana; line-height: 25.6px;\">
                    <tbody style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\">
                    <tr style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 5px 0px; margin: 0px;\">
                        <td style=\"font-family: arial, sans-serif; margin: 0px; text-align: left; font-size: 14px; line-height: 22.4px; color: #817c8d; padding: 3px 3px 3px 0px;\" colspan=\"3\"><a style=\"color: #1155cc; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; padding: 0px; margin: 0px auto;\" href=\"https://www.pickcenter.it/\"><img class=\"m_-36167933669901069m_-8609610622824970058m_5889173275609122587gmail-CToWUd CToWUd\" style=\"margin: 0px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px;\" src=\"https://ci3.googleusercontent.com/proxy/QcQtNrfY-AJMm7qM47rpdKt5np89yMP5WvtEdvFWfk9v65-jmlaJNUkIiSOHrg8A9VjQAgNur109K16hjIU0QdxrTPeaAaqXb9mbQuiMWde9Yzr6326LAme_FgSQvhA2VQ=s0-d-e1-ft#https://www.pickcenter.it/wp-content/uploads/2017/08/template_logo_pick.png\" alt=\"\" width=\"256\" height=\"56\" /></a></td>
                    </tr>
                    <tr style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 5px 0px; margin: 0px;\">
                        <td style=\"font-family: arial, sans-serif; margin: 0px; padding: 5px; font-size: 14px; line-height: 22.4px; color: #817c8d;\"><img style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\" src=\"https://www.pickcenter.it/quotesimgs/Pick-center-Eur-My-Business-Virtual-Tour-28.jpg\" alt=\"\" width=\"230\" /></td>
                        <td style=\"font-family: arial, sans-serif; margin: 0px; padding: 5px; font-size: 14px; line-height: 22.4px; color: #817c8d;\"><img style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\" src=\"https://www.pickcenter.it/quotesimgs/Exclusiveoffice1.jpg\" alt=\"\" width=\"230\" /></td>
                        <td style=\"font-family: arial, sans-serif; margin: 0px; padding: 5px; font-size: 14px; line-height: 22.4px; color: #817c8d;\"><img style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\" src=\"https://www.pickcenter.it/quotesimgs/Ufficio-1-posto.jpg\" alt=\"\" width=\"230\" /></td>
                    </tr>
                    <tr style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 5px 0px; margin: 0px;\">
                        <td style=\"font-family: arial, sans-serif; margin: 0px; text-align: justify; font-size: 14px; line-height: 22.4px; color: #817c8d; padding: 3px 3px 3px 0px;\" colspan=\"3\">
                            <p><br style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;\" /><span style=\"font-family: verdana, sans-serif; font-size: 13px; line-height: 20.8px; color: #444444; padding: 0px; margin: 0px auto;\"><span style=\"font-family: verdana, sans-serif; font-size: 13px; line-height: 20.8px; color: #444444; padding: 0px; margin: 0px auto;\">{$body}<br/>
                        </td>
                    </tr>
                    <tr style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 5px 0px; margin: 0px;\">
                        <td style=\"font-family: arial, sans-serif; margin: 0px; padding: 5px; font-size: 14px; line-height: 22.4px; color: #817c8d;\"><img  style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\" src=\"https://www.pickcenter.it/wp-content/uploads/2017/08/template_quot_eventi.png\" alt=\"\" width=\"180\" /></td>
                        <td style=\"font-family: arial, sans-serif; margin: 0px; padding: 5px; font-size: 14px; line-height: 22.4px; color: #817c8d;\"><strong style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;\">Cerchi una Sala Riunioni?<strong style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;\"><br style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;\" /><a style=\"color: #1155cc; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; padding: 0px; margin: 0px auto;\" href=\"https://www.pickcenter.it/booking\"><img style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\" src=\"https://www.pickcenter.it/wp-content/uploads/2017/08/Prenota-button.jpg\" alt=\"\" width=\"250\" /></a><br style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;\" /><span style=\"font-size: 13px; font-family: Arial, Helvetica, sans-serif; line-height: 20.8px; color: #444444; padding: 0px; margin: 0px auto;\">Da 4 a 70 persone, allestite per meeting, conferenze, corsi di formazione</span></strong></strong></td>
                        <td style=\"font-family: arial, sans-serif; margin: 0px; padding: 5px; font-size: 14px; line-height: 22.4px; color: #817c8d;\"><img style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\" src=\"https://www.pickcenter.it/wp-content/uploads/2017/08/template_boezio.png\" alt=\"\" width=\"180\" /></td>
                    </tr>
                    </tbody>
                </table>
                <table style=\"color: #000000; font-size: 16px; width: 741px; background-color: #ff9900; margin: 0px; padding: 10px; text-align: center; font-family: Verdana; line-height: 25.6px;\">
                    <tbody style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;\">
                        <tr style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 5px 0px; margin: 0px;\">
                            <td style=\"font-family: arial, sans-serif; margin: 0px; font-size: 14px; line-height: 22.4px; color: #817c8d; padding: 3px 3px 3px 0px;\"><span style=\"color: #ffffff; font-weight: bold; font-size: 18px; font-family: Arial, Helvetica, sans-serif; line-height: 28.8px; padding: 0px; margin: 0px auto;\"><a style=\"color: #ffffff; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; padding: 0px; margin: 0px auto;\" href=\"https://www.pickcenter.it/promozioni/\">Scopri tutte le nostre Promozioni</a><br style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;\" /></span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            </div>
            </div>
            </div>
            </div>
            </div>
            </div>
            </div>
            </div>
            <div class=\"mozaik-clear\" style=\"font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px; height: 0px;\">&nbsp;</div>
            </div>
                <hr>
            <div style=\"vertical-align: bottom;\"><span style=\"font-size: xx-small;\"><img src=\"http://www.pickcenter.it/logoemail.jpg\" alt=\"\" /> &nbsp;&nbsp; <a href=\"https://www.youtube.com/channel/UCYXvx7VHMqKX_tFkqwhWZEA\"><img style=\"border: 0px;\" src=\"http://www.pickcenter.it/img/firma2012/yt.png\" alt=\"youtube\" /></a> <a href=\"https://www.facebook.com/pickcenter.hub/\"><img style=\"border: 0px;\" src=\"http://www.pickcenter.it/img/firma2012/fb.png\" alt=\"facebook\" /></a> <a href=\"http://twitter.com/pickcenter\"><img style=\"border: 0px;\" src=\"http://www.pickcenter.it/img/firma2012/tw.png\" alt=\"twitter\" /></a> <a href=\"http://www.linkedin.com/company/pick-center\"><img style=\"border: 0px;\" src=\"http://www.pickcenter.it/img/firma2012/li.png\" alt=\"linkedin\" /></a>&nbsp;<a href=\"https://www.instagram.com/pickcenter_smartworkinghub/\"><img style=\"font-size: 1.3em;\" src=\"http://www.pickcenter.it/wp-content/uploads/2016/10/firma_ig.jpg\" alt=\"\" /></a></span></div>
            <div style=\"margin: 0px; padding: 0px; font-size: 10px;\"><span style=\"font-size: xx-small;\">Your Workplace</span></div>
            <div style=\"margin: 0px; padding: 0px; font-size: 10px;\"><span style=\"font-size: xx-small;\">Via Attilio Regolo 19</span><br /><span style=\"font-size: xx-small;\">Via Boezio 6</span><br /><span style=\"font-size: xx-small;\">Piazza Marconi 15</span></div>
            <div style=\"margin: 0px; padding: 0px; font-size: 10px;\"><span style=\"font-size: xx-small;\">Servizio Clienti: <span style=\"color: #ff7f00;\"><strong>800 189 099</strong></span></span></div>
            <div style=\"margin: 0px; padding: 0px; font-size: 10px;\"><span style=\"font-size: xx-small;\">Tel +39 06 328031</span></div>
            <div style=\"margin: 0px; padding: 0px; font-size: 10px;\"><span style=\"font-size: xx-small;\">Fax +39 06 32803282</span></div>
            <div style=\"margin: 0px; padding: 0px; font-size: 10px;\"><span style=\"font-size: xx-small;\">Pick Center Roma srl unipersonale, Via A. Regolo 19, 00192 - Roma</span></div>
            <div style=\"margin: 0px; padding: 0px; font-size: 10px;\"><span style=\"font-size: xx-small;\">C.F. e P.IVA: 12599371007</span></div>
            <div style=\"margin: 10px 0px; color: #008800; font-size: 10px; font-weight: bold;\"><span style=\"font-size: xx-small;\">Non mi stampare se non &egrave; necessario: proteggiamo l'ambiente! / Do not print me unless necessary; let's be environment-friendly!</span></div>
            <div style=\"margin: 0px; padding: 0px; color: #dddddd; font-size: 10px;\"><span style=\"font-size: xx-small;\"><em>--- Clausola di esclusione di responsabilit&agrave; ---</em></span></div>
            <div style=\"margin: 0px; padding: 0px; color: #dddddd; font-size: 10px;\"><span style=\"font-size: xx-small;\"><em><br />Pick Center Roma S.r.l. Unipersonale&nbsp;non pu&ograve; assicurare la sicurezza e la riservatezza di comunicazioni non criptate trasmesse via E-mail. Pick Center Roma S.r.l. Unipersonale non assume alcuna responsabilit&agrave; per danni derivanti da accesso non autorizzato, rivelazioni o interferenze che possano aversi durante la trasmissione.</em></span></div>
            <div style=\"margin: 0px; padding: 0px; color: #dddddd; font-size: 10px;\"><span style=\"font-size: xx-small;\"><em>--- Exclusion clause of responsability ---</em></span></div>
            <div style=\"margin: 0px; padding: 0px; color: #dddddd; font-size: 10px;\"><span style=\"font-size: xx-small;\"><em>Pick Center Roma S.r.l. Unipersonale can not guarantee the security and the discretion of communications sent by E-mail that have not been encrypted. Pick Center Roma S.r.l. Unipersonale does not undertake responsability for any harm caused by wildcast access, disclosure or interference that may happen during transmission.</em></span></div>
        ";
        echo $mailbody;
    }

    //body per la mail iscrizione al club
    public function bodyWelcome($name,$welcomepoints,$newsletterpoints,$username,$password) {

        $mail['body'] = "
        Ciao <span style='font-weight: bold'>{$name}</span>,<BR/>
        benvenuto nel Pick Center Club, il programma che ti permette di accedere a sconti e vantaggi esclusivi e che ti premia per la tua fedelt&agrave;.<BR/>
        Siamo felici di assegnarti subito <span style='font-weight: bold'>{$welcomepoints} punti</span> come omaggio di benvenuto; sono gi&agrave; disponibili e li potrai utilizzare fin dal tuo prossimo acquisto.<br>
        E se ancora non sei <a href='https://www.pickcenter.it/contatti/#footer' target='_blank'>iscritto alla nostra Newsletter</a>, fallo subito! Sarai aggiornato su tutte le novit&agrave; e sulle promozioni di Pick Center e riceverai un ulteriore bonus di <span style='font-weight: bold'>{$newsletterpoints} punti</span>.<br>
        Ti ricordiamo che puoi vedere <a href='{$this->rulebookUrl}' target='_blank'>qui</a> tutte le modalit&agrave; di utilizzo dei tuoi punti.  Ti aspettiamo.<br>
        <p></p>
        Per effettuare il primo accesso:<br/>
        <span style='font-weight: bold'>Sito</span>: <a href='http://booking.pickcenter.it' target='_blank'>booking.pickcenter.it</a><br/>
        <span style='font-weight: bold'>Username</span>: {$username}<br />
        <span style='font-weight: bold'>Password</span>: {$password}<br />
        <p></p>
        <P style='font-weight: bold'>Lo staff di Pick Center</P>
        ";
        $mail['subject'] = 'Pick Center - Sistema a punti: Accredito';
        return $mail;
    }

    //body per la mail di saldo del Club con descrizione promo
    public function bodyBalancePromo($name,$totalpoints,$promotext) {
      $mail['body'] = "
      Ciao <span style='font-weight: bold'>{$name}</span>,<BR/>
      grazie per essere iscritto al Pick Center Club. Il tuo saldo punti ad oggi &egrave; di <span style='font-weight: bold'>{$totalpoints} punti</span>.<br/>
      Ti ricordiamo che puoi utilizzare i tuoi punti per ottenere sconti e vantaggi sull&apos;acquisto di servizi Pick Center. Prova ad esempio {$promotext} o guarda <a href='{$this->rulebookUrl}'>qui</a> tutte le modalit&agrave; di utilizzo dei tuoi punti. Ti aspettiamo.
      <p style='font-weight: bold'>Lo staff di Pick Center</p>
      ";
      $mail['subject'] = "Pick Center - Sistema a Punti: Saldo";
      return $mail;
    }

    //body per la mail di saldo
    public static function bodyBalance($name,$totalpoints) {
        $mail['body'] = "
        Ciao <span style='font-weight: bold'>{$name}</span>,<BR/>
        grazie per essere un cliente Pick Center e per essere iscritto al Pick Center Club.<br />
        Il tuo saldo punti ad oggi &egrave; di <span style='font-weight: bold'>{$totalpoints} punti</span>. Ti ricordiamo che per utilizzare i tuoi punti ed ottenere sconti e vantaggi sull&apos;acquisto di servizi Pick Center, devi segnalarlo nella tua area riservata disponibile sul sito <a href='https:\\www.pickcenter.it' target='_blank'>www.pickcenter.it</a>. Ti aspettiamo.
        <p style='font-weight: bold'>Lo staff di Pick Center</p>
        ";
        $mail['subject'] = "Pick Center - Sistema a Punti: Saldo";
        return $mail;
    }

    //body per accredito punti
    public function bodyCredit($name,$points) {
        $mail['body'] = "
        Ciao <span style='font-weight: bold'>{$name}</span>,<BR/>
        grazie per essere un cliente Pick Center e per essere iscritto al Pick Center Club.<br />
        Oggi abbiamo accreditato sul tuo conto Club <span style='font-weight: bold'>{$points} punti</span> che potrai utilizzare per ottenere sconti e vantaggi sull&apos;acquisto di servizi Pick Center.<br />
        Guarda <a href='{$this->rulebookUrl}' target='_blank'>qui</a> tutte le modalit&agrave; di utilizzo dei tuoi punti. Ti aspettiamo. 
        <p style='font-weight: bold'>Lo staff di Pick Center</p>
        ";
        $mail['subject'] = "Pick Center - Sistema a Punti: Accredito";
        return $mail;
    }

    //body per mancato accredito
    public function bodyMissed($name) {
      $mail['body'] = "
      Ciao <span style='font-weight: bold'>{$name}</span>,<BR/>
      grazie per essere un cliente Pick Center. Sai che la tua fedelt&agrave; poteva essere premiata con i Punti del Pick Center Club?<br>
      Effettua subito l'accesso al Pick Center Club, il programma che ti permette di accedere a sconti e vantaggi esclusivi, e non perdere l&apos;occasione di iniziare a risparmiare.<br>
      Scopri come cliccando <a href='{$this->rulebookUrl}' target='_blank'>qui</a>. Ti aspettiamo. 
              <p></p>
        Puoi accedere al sito <a href='http://booking.pickcenter.it' target='_blank'>booking.pickcenter.it</a> utilizzando il tuo indirizzo email come nome utente. Se non ricordi la password <a href='https://www.pickcenter.it/mio-account/lost-password/' target='_blank'>clicca qui</a>.
        <p></p>
      <p style='font-weight: bold'>Lo staff di Pick Center</p>
      ";
      $mail['subject'] = "Pick Center - Sistema a Punti: Accedi";
      return $mail;
    }

    //body per mail compleanno
    public function bodyBirthday($name,$points) {
      $mail['body'] = "
      Ciao <span style='font-weight: bold'>{$name}</span>, tanti auguri!<br />
      Vogliamo farti festeggiare al meglio il tuo compleanno e per questo ti regaliamo <span style='font-weight: bold'>{$points} punti</span> del Pick Center Club. Una bella sorpresa per te che ti permetter&agrave; di ottenere ancora pi&ugrave; sconti e vantaggi esclusivi.<br/>
      Passa una splendida giornata!
      <p style='font-weight: bold'>Lo staff di Pick Center</p>
      ";
      $mail['subject'] = "Pick Center - Sistema a Punti: Buon compleanno!";
      return $mail;
    }

    //body per mail anniversario
    public function bodyAnniversary($name,$points) {
        $mail['body'] = "
        Ciao <span style='font-weight: bold'>{$name}</span>, tanti auguri!<br />
        S&igrave;, sappiamo che oggi non &egrave; il tuo compleanno, ma &egrave; ugualmente un giorno speciale: oggi &egrave; l&apos;anniversario dell&apos;inizio della relazione tra Pick Center e la tua azienda, e noi teniamo molto ai nostri Clienti.<br/>
        Per questo ti regaliamo <span style='font-weight: bold'>{$points} punti</span> del Pick Center Club. Una bella sorpresa per la tua azienda che le permetter&agrave; di ottenere ancora pi&ugrave; sconti e vantaggi esclusivi.<br/>
        Un augurio a tutti voi!<br/>
        <p style='font-weight: bold'>Lo staff di Pick Center</p>
        ";
        $mail['subject'] = "Pick Center - Sistema a Punti: Buon Anniversario!";
        return $mail;
    }

    //body per mail continuità contrattuale
    public function bodyFidelity($name,$points,$cycle) {
        $mail['body'] = "
        Ciao <span style='font-weight: bold'>{$name}</span>,<BR/>
        grazie per essere un cliente Pick Center e per essere iscritto al Pick Center Club: il club che premia la tua fedelt&agrave;.
        Siamo infatti molto felici di poterti assegnare <span style='font-weight: bold'>{$points} punti</span> per aver superato <span style='font-weight: bold'>{$cycle} mesi</span> continuativi di contratto; potrai cos&igrave; ottenere ancora pi&ugrave; sconti e vantaggi esclusivi.<br/>
        Ti ricordiamo che puoi vedere tutte le modalit&agrave; di utilizzo dei tuoi punti <a href='{$this->rulebookUrl}' target='_blank'>qui</a>. Ti aspettiamo.
        <p style='font-weight: bold'>Lo staff di Pick Center</p> ";
        $mail['subject'] = "Pick Center - Sistema a Punti: Bonus fedeltà";
        return $mail;
    }

}