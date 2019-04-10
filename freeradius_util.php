<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 08/10/2018
 * Time: 16:11
 */
require 'tech/connect.php';
require 'tech/radius_functions.php';

$sql_contatti = "select wifi_wifi_accounts.*, email_lead.email_address, concat(first_name,' ',last_name) as cliente from wifi_wifi_accounts
left join wifi_wifi_accounts_leads_c on wifi_wifi_accounts.id = wifi_wifi_accounts_leads_c.wifi_wifi_accounts_leadswifi_wifi_accounts_idb
left join email_lead on wifi_wifi_accounts_leadsleads_ida = email_lead.id
where wifi_wifi_accounts.deleted = '0' and primario = '1'";

//cerco nuovi account da contatto, li registro e li attivo
$array_acc2create = $conn_crm->query($sql_contatti);

while ($account = $array_acc2create->fetch_assoc()) {

    $is_present = $conn_radius->query("SELECT id FROM radcheck WHERE username = '".$account['user_name']."'")->num_rows;

    $username = $account['user_name'];
    $password = $account['password'];
    $expdate = date('M d Y', strtotime($account['expiration_date']));
    $devices = $account['sim_uses'];
    $maxup = $account['up_speed'];
    $maxdown = $account['down_speed'];
    $email = $account['email_address'];
    $name = $account['cliente'];
    $center = $account['center']; //reg,boe,eur,all
    switch ($center) {
        case 'reg': $center_mail = 'regolo@pickcenter.com'; break;
        case 'boe'; $center_mail = 'boezio@pickcenter.com'; break;
        case 'eur': $center_mail = 'eur@pickcenter.com'; break;
        case 'all': $center_mail = 'info@pickcenter.com'; break;
    }

    if ($account['status'] == 'new' && $is_present != 0) {
        aggiorna_crm($conn_crm,$account['id'],'Username duplicato','new');; //aggiorno descrizione erp
        exit;
    }

    if ($account['status'] == 'new' && $is_present == 0) {
        radius_create($conn_radius,$username,$password,strtotime($account['expiration_date']),$devices,$maxup,$maxdown);
        aggiorna_crm($conn_crm,$account['id'],'','enabled');
        mailsend_account($email,$name,$username,$password,$devices,$center_mail);
    }

    if ( (strtotime($account['expiration_date']) < strtotime('now')) && $account['status'] != 'disabled' && strtotime($account['expiration_date'])!=0) {
        radius_delete($conn_radius,$username);
        aggiorna_crm($conn_crm,$account['id'],'Account Scaduto','disabled');
    }

    if ($account['status'] == 'disabled' && $is_present != 0) {
        radius_delete($conn_radius,$username);
        aggiorna_crm($conn_crm,$account['id'],'Account disattivato manualmente','disabled');
    }

    if ($account['status'] == 'edit') {
        radius_delete($conn_radius,$username);
        radius_create($conn_radius,$username,$password,strtotime($account['expiration_date']),$devices,$maxup,$maxdown);
        aggiorna_crm($conn_crm,$account['id'],'Account modificato','enabled');
    }
}

//controllo account assegnati ad aziende
$sql_aziende = "select wifi_wifi_accounts.*, email_address, email_account.name as cliente from wifi_wifi_accounts
left join wifi_wifi_accounts_accounts_c on wifi_wifi_accounts.id = wifi_wifi_accounts_accounts_c.wifi_wifi_accounts_accountswifi_wifi_accounts_idb
left join email_account on wifi_wifi_accounts_accountsaccounts_ida = email_account.id
where wifi_wifi_accounts.deleted = '0' and wifi_wifi_accounts_accountsaccounts_ida is not NULL";

//cerco nuovi account da aziende, li registro e li attivo
$array_acc2create = $conn_crm->query($sql_aziende);


while ($account = $array_acc2create->fetch_assoc()) {

    $is_present = $conn_radius->query("SELECT id FROM radcheck WHERE username = '".$account['user_name']."'")->num_rows;

    $username = $account['user_name'];
    $password = $account['password'];
    $expdate = date('M d Y', strtotime($account['expiration_date']));
    $devices = $account['sim_uses'];
    $maxup = $account['up_speed'];
    $maxdown = $account['down_speed'];
    $email = $account['email_address'];
    $name = $account['cliente'];
    $center = $account['center']; //reg,boe,eur,all
    switch ($center) {
        case 'reg': $center_mail = 'regolo@pickcenter.com'; break;
        case 'boe'; $center_mail = 'boezio@pickcenter.com'; break;
        case 'eur': $center_mail = 'eur@pickcenter.com'; break;
        case 'all': $center_mail = 'info@pickcenter.com'; break;
    }

    if ($account['status'] == 'new' && $is_present != 0) {
        aggiorna_crm($conn_crm,$account['id'],'Username duplicato','new');; //aggiorno descrizione erp
        exit;
    }

    if ($account['status'] == 'new' && $is_present == 0) {
        radius_create($conn_radius,$username,$password,strtotime($account['expiration_date']),$devices,$maxup,$maxdown);
        aggiorna_crm($conn_crm,$account['id'],'','enabled');
        mailsend_account($email,$name,$username,$password,$devices,$center_mail);

    }

    if ( (strtotime($account['expiration_date']) < strtotime('now')) && $account['status'] != 'disabled' && strtotime($account['expiration_date'])!=0) {
        radius_delete($conn_radius,$username);
        aggiorna_crm($conn_crm,$account['id'],'Account Scaduto','disabled');
    }

    if ($account['status'] == 'disabled' && $is_present != 0) {
        radius_delete($conn_radius,$username);
        aggiorna_crm($conn_crm,$account['id'],'Account disattivato manualmente','disabled');
    }

    if ($account['status'] == 'edit') {
        radius_delete($conn_radius,$username);
        radius_create($conn_radius,$username,$password,strtotime($account['expiration_date']),$devices,$maxup,$maxdown);
        aggiorna_crm($conn_crm,$account['id'],'Account modificato','enabled');
    }
}

