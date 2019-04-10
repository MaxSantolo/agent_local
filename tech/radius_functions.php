<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 09/10/2018
 * Time: 15:21
 */
require 'class/PHPMailerAutoload.php';

function radius_delete($conn,$username) {
    $conn->query("delete from radcheck where username = '".$username."'");
    $conn->query("delete from radreply where username = '".$username."'");
}

function radius_create($conn,$username,$password,$expdate,$devices,$maxup,$maxdown) {
    $conn->query("INSERT INTO radcheck (username, attribute, op, value) VALUES ('".$username."', 'User-Password', ':=', '".$password."')");
    if ($expdate !=0) {
        $conn->query("INSERT INTO radcheck (username, attribute, op, value) VALUES ('".$username."', 'Expiration', ':=', '".date('M d Y', $expdate)."')"); //data di scadenza solo se la data è espressa
    }
    $conn->query("INSERT INTO radcheck (username, attribute, op, value) VALUES ('".$username."', 'Simultaneous-Use', ':=', '".$devices."')");
    $conn->query("INSERT INTO radreply (username, attribute, op, value) VALUES ('".$username."', 'Idle-Timeout', ':=', '28800')");
    $conn->query("INSERT INTO radreply (username, attribute, op, value) VALUES ('".$username."', 'Acct-Interim-Interval', ':=', '60')");
    $conn->query("INSERT INTO radreply (username, attribute, op, value) VALUES ('".$username."', 'WISPr-Bandwidth-Max-Up', ':=', '".$maxup."')");
    $conn->query("INSERT INTO radreply (username, attribute, op, value) VALUES ('".$username."', 'WISPr-Bandwidth-Max-Down', ':=', '".$maxdown."')");
}

function aggiorna_crm($conn,$id_wifi,$description,$status) {
    //$status validi: enabled, disabled, edit, new
    $conn->query("UPDATE wifi_wifi_accounts SET status = '".$status."', description = '".$description."' WHERE id = '".$id_wifi."'");
}

function mailsend_account($email,$name,$username,$password,$devices,$center_mail) {


    $mailbody = '<div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px 30px; margin: 0px auto; width: 800px; background-color: #fafafa;">
                <table style="font-size: 16px; padding: 10px; margin: 0px; width: 748px; text-align: center; font-family: Verdana; line-height: 25.6px; color: #444444;">
                <tbody style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;">
                <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 5px 0px; margin: 0px;">
                <td style="font-family: arial, sans-serif; margin: 0px; text-align: left; font-size: 14px; line-height: 22.4px; color: #817c8d; padding: 3px 3px 3px 0px;" colspan="3"><a style="color: #1155cc; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; padding: 0px; margin: 0px auto;" href="https://www.pickcenter.it/"><img class="m_-3992486088663327944m_-1853165144588226077m_-6750543036778682475gmail-CToWUd CToWUd" style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;" src="https://ci3.googleusercontent.com/proxy/QcQtNrfY-AJMm7qM47rpdKt5np89yMP5WvtEdvFWfk9v65-jmlaJNUkIiSOHrg8A9VjQAgNur109K16hjIU0QdxrTPeaAaqXb9mbQuiMWde9Yzr6326LAme_FgSQvhA2VQ=s0-d-e1-ft#https://www.pickcenter.it/wp-content/uploads/2017/08/template_logo_pick.png" alt="" width="300" /></a></td>
                </tr>
                <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 5px 0px; margin: 0px;">
                <td style="font-family: arial, sans-serif; margin: 0px; padding: 5px; font-size: 14px; line-height: 22.4px; color: #817c8d;"><img src="https://www.pickcenter.it/wp-content/uploads/2016/10/04_dayoffice.jpg" alt="" width="230" /></td>
                <td style="font-family: arial, sans-serif; margin: 0px; padding: 5px; font-size: 14px; line-height: 22.4px; color: #817c8d;"><img src="https://www.pickcenter.it/wp-content/uploads/2017/08/Pick-center-Eur-My-Business-Virtual-Tour-08.jpg" alt="" width="230" /></td>
                <td style="font-family: arial, sans-serif; margin: 0px; padding: 5px; font-size: 14px; line-height: 22.4px; color: #817c8d;"><img src="https://www.pickcenter.it/wp-content/uploads/2017/08/Pick-center-Boezio-My-Business-Virtual-Tour-13.jpg" alt="" width="230" /></td>
                </tr>
                <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 5px 0px; margin: 0px;">
                <td style="font-family: arial, sans-serif; margin: 0px; text-align: justify; font-size: 14px; line-height: 22.4px; color: #817c8d; padding: 3px 3px 3px 0px;" colspan="3"><br style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;" /><span style="font-family: verdana, sans-serif; font-size: 13px; line-height: 20.8px; color: #444444; padding: 0px; margin: 0px auto;">
                Egregio dr./dr.ssa '.$name.',<br style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;" />
                <br style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;" />
                benvenuto/a nel sistema WiFi di Pick Center.<BR />Collegati a <strong>PCK_SPOT</strong> utilizzando<BR/>
                <P>Username: <strong>'.$username.'</strong></P>
                <P>Password: <strong>'.$password.'</strong></P>
                <P>L\'account &egrave; valido per '.$devices.' utenti o apparecchi diversi.<P>
                <br style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;" />
                Restiamo a disposizione per qualsiasi ulteriore <a href="mailto:info@pickcenter.com">informazione</a>.<br style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;" /><br style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;" />
                </span></td>
                </tr>
                <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 5px 0px; margin: 0px;">
                <td style="font-family: arial, sans-serif; margin: 0px; padding: 5px; font-size: 14px; line-height: 22.4px; color: #817c8d;"><img src="https://www.pickcenter.it/wp-content/uploads/2017/08/template_quot_eventi.png" alt="" width="180" /></td>
                <td style="font-family: arial, sans-serif; margin: 0px; padding: 5px; font-size: 14px; line-height: 22.4px; color: #817c8d;"><strong style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;">Cerchi una Sala Riunioni?<strong style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;"><br style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;" /><a style="color: #1155cc; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; padding: 0px; margin: 0px auto;" href="https://www.pickcenter.it/booking"><img class="m_-3992486088663327944m_-1853165144588226077m_-6750543036778682475gmail-CToWUd CToWUd" style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;" src="https://ci5.googleusercontent.com/proxy/-MyqqSU6gdJKLRwNuvAzOh4_hoYDW6LyoB74Wx_2CzFG5P8_Tnq619VajMHBUVwWbH4Xl-TWmFWoAH32oYg-oUBq1WUkVdWB6CH2gt77aoSazB7eRfnD1_RtnxOu=s0-d-e1-ft#https://www.pickcenter.it/wp-content/uploads/2017/08/Prenota-button.jpg" alt="" width="250" /></a><br style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px auto;" /><span style="font-size: 13px; font-family: Arial, Helvetica, sans-serif; line-height: 20.8px; color: #444444; padding: 0px; margin: 0px auto;">Da 4 a 70 persone, allestite per meeting, conferenze, corsi di formazione</span></strong></strong></td>
                <td style="font-family: arial, sans-serif; margin: 0px; padding: 5px; font-size: 14px; line-height: 22.4px; color: #817c8d;"><img src="https://www.pickcenter.it/wp-content/uploads/2017/08/template_boezio.png" alt="" width="180" /></td>
                </tr>
                </tbody>
                </table>
                <table style="font-size: 16px; width: 741px; background-color: #ff9900; margin: 0px; padding: 10px; text-align: center; font-family: Verdana; line-height: 25.6px; color: #444444;">
                <tbody style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px;">
                <tr style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 5px 0px; margin: 0px;">
                <td style="font-family: arial, sans-serif; margin: 0px; font-size: 14px; line-height: 22.4px; color: #817c8d; padding: 3px 3px 3px 0px;"><span style="color: #ffffff; font-weight: bold; font-size: 18px; font-family: Arial, Helvetica, sans-serif; line-height: 28.8px; padding: 0px; margin: 0px auto;"><a style="color: #ffffff; font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; padding: 0px; margin: 0px auto;" href="https://www.pickcenter.it/promozioni/">Scopri tutte le nostre Promozioni</a></span></td>
                </tr>
                </tbody>
                </table>
                <div style="font-family: Arial, Helvetica, sans-serif; font-size: 14px; line-height: 22.4px; color: #444444; padding: 0px; margin: 0px; height: 0px;">&nbsp;</div>
                </div>
                <p><br /><br /></p>
                <div style="vertical-align: bottom;"><span style="font-size: xx-small;"><img src="http://www.pickcenter.it/logoemail.jpg" alt="" /> &nbsp;&nbsp; <a href="https://www.youtube.com/channel/UCYXvx7VHMqKX_tFkqwhWZEA"><img style="border: 0px;" src="http://www.pickcenter.it/img/firma2012/yt.png" alt="youtube" /></a> <a href="https://www.facebook.com/pickcenter.hub/"><img style="border: 0px;" src="http://www.pickcenter.it/img/firma2012/fb.png" alt="facebook" /></a> <a href="http://twitter.com/pickcenter"><img style="border: 0px;" src="http://www.pickcenter.it/img/firma2012/tw.png" alt="twitter" /></a> <a href="http://www.linkedin.com/company/pick-center"><img style="border: 0px;" src="http://www.pickcenter.it/img/firma2012/li.png" alt="linkedin" /></a>&nbsp;<a href="https://www.instagram.com/pickcenter_smartworkinghub/"><img style="font-size: 1.3em;" src="http://www.pickcenter.it/wp-content/uploads/2016/10/firma_ig.jpg" alt="" /></a></span></div>
                <div style="margin: 0px; padding: 0px; font-size: 10px;"><span style="font-size: xx-small;">Your Workplace</span></div>
                <div style="margin: 0px; padding: 0px; font-size: 10px;"><span style="font-size: xx-small;">Via Attilio Regolo 19</span><br /><span style="font-size: xx-small;">Via Boezio 6</span><br /><span style="font-size: xx-small;">Piazza Marconi 15</span></div>
                <div style="margin: 0px; padding: 0px; font-size: 10px;"><span style="font-size: xx-small;">Servizio Clienti: <span style="color: #ff7f00;"><strong>800 189 099</strong></span></span></div>
                <div style="margin: 0px; padding: 0px; font-size: 10px;"><span style="font-size: xx-small;">Tel +39 06 328031</span></div>
                <div style="margin: 0px; padding: 0px; font-size: 10px;"><span style="font-size: xx-small;">Fax +39 06 32803282</span></div>
                <div style="margin: 0px; padding: 0px; font-size: 10px;"><span style="font-size: xx-small;">Pick Center Roma srl unipersonale, Via A. Regolo 19, 00192 - Roma</span></div>
                <div style="margin: 0px; padding: 0px; font-size: 10px;"><span style="font-size: xx-small;">C.F. e P.IVA: 12599371007</span></div>
                <div style="margin: 10px 0px; color: #008800; font-size: 10px; font-weight: bold;"><span style="font-size: xx-small;">Non mi stampare se non &egrave; necessario: proteggiamo l\'ambiente! / Do not print me unless necessary; let\'s be environment-friendly!</span></div>
                <div style="margin: 0px; padding: 0px; color: #dddddd; font-size: 10px;"><span style="font-size: xx-small;"><em>--- Clausola di esclusione di responsabilit&agrave; ---</em></span></div>
                <div style="margin: 0px; padding: 0px; color: #dddddd; font-size: 10px;"><span style="font-size: xx-small;"><em><br />Pick Center Roma S.r.l. Unipersonale&nbsp;non pu&ograve; assicurare la sicurezza e la riservatezza di comunicazioni non criptate trasmesse via E-mail. Pick Center Roma S.r.l. Unipersonale non assume alcuna responsabilit&agrave; per danni derivanti da accesso non autorizzato, rivelazioni o interferenze che possano aversi durante la trasmissione.</em></span></div>
                <div style="margin: 0px; padding: 0px; color: #dddddd; font-size: 10px;"><span style="font-size: xx-small;"><em>--- Exclusion clause of responsability ---</em></span></div>
                <div style="margin: 0px; padding: 0px; color: #dddddd; font-size: 10px;"><span style="font-size: xx-small;"><em>Pick Center Roma S.r.l. Unipersonale can not guarantee the security and the discretion of communications sent by E-mail that have not been encrypted. Pick Center Roma S.r.l. Unipersonale does not undertake responsability for any harm caused by wildcast access, disclosure or interference that may happen during transmission.</em></span></div>';

    $mail = new PHPMailer();


    $mail->IsSMTP();
    $mail->CharSet='UTF-8';
    $mail->Host = '10.20.20.227';
    $mail->SMTPAuth = false;
    $mail->From = "info@pickcenter.com";
    $mail->FromName = "Pick Center sistema notifica";
    $mail->AddReplyTo('info@pickcenter.com', 'Informazioni');
    $mail->WordWrap = 50;
    $mail->IsHTML(true);
    $mail->Subject = 'Benvenuto nella rete WiFi di Pick Center';
    $mail->AltBody = 'Il messaggio &egrave; in formato HTML';
    $mail->AddAddress($email,$name);
    $mail->AddCC($center_mail,'Pick');
    $mail->Body = $mailbody;
    $mail->send();
}


