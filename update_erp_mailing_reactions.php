<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 06/08/2018
 * Time: 15:41
 */

include "tech/connect.php";

$now = date('Y-m-d');
$admin_id = '1';

$reaction_array = $conn->query("SELECT campaigns_react.email, group_concat(reazione) AS react, MAX(data) AS react_date, id_campagna, name, title, htmlcontent, scheduledat, url, type, email_check.id_erp
FROM campaigns_react, campaigns, email_check
WHERE id = id_campagna AND campaigns_react.email = email_check.email AND email_check.id_erp != '' AND campaigns.id != 0
GROUP BY campaigns_react.email ORDER BY email, reazione ASC");


while ($row = $reaction_array->fetch_assoc()) {

    $id_reazione = $conn_crm->query("SELECT id FROM csb_reazioni order by id desc limit 1")->fetch_assoc();
    $id_lead_reazione = $conn_crm->query("SELECT id FROM csb_reazioni_leads_c order by id desc limit 1")->fetch_assoc();
    $nuovo_id_reazione = ++$id_reazione['id'];
    $nuovo_id_lead_reazione = ++$id_lead_reazione['id'];

    $check_presence = $conn_crm->query("SELECT csb_reazioni.id, csb_reazioni_leads_c.csb_reazioni_leadsleads_ida
                                    from csb_reazioni, csb_reazioni_leads_c
                                    WHERE name = '".$row['name']."' and title = '".$row['title']."' and campaign_react_date = '".$row['react_date']."'
                                    and csb_reazioni_leads_c.csb_reazioni_leadsleads_ida = '".$row['id_erp']."'
                                    and csb_reazioni.deleted = 0 and csb_reazioni.id = csb_reazioni_leads_c.csb_reazioni_leadscsb_reazioni_idb"); //CONTROLLA SE ESISTE NEL CRM

    if ($check_presence->num_rows == 0) {

    $conn_crm->query("INSERT INTO csb_reazioni (name, title, campaign_react_date, id, date_entered, date_modified, created_by, id_sb, type, reazione) VALUES
                    ('".$row['name']."','".$row['title']."','".$row['react_date']."', '".$nuovo_id_reazione."', '".$now."', '".$now."', '1', '".$row['id']."', 'email', '".$row['react']."' )"); //aggiunge i campi base alla tabella reazioni

    $conn_crm->query("INSERT INTO csb_reazioni_cstm (id_c, url_sb_c) VALUES ('".$nuovo_id_reazione."', '".$row['url']."')"); //aggiunge l'URL

    $conn_crm->query("INSERT INTO csb_reazioni_leads_c (csb_reazioni_leadsleads_ida, csb_reazioni_leadscsb_reazioni_idb, id, date_modified)
                     VALUES ('".$row['id_erp']."', '".$nuovo_id_reazione."', '".$nuovo_id_lead_reazione."', '".$now."')"); //crea legame logico fra contatto e reazione

    }
}











