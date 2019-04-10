
<head>
    <title>GESTIONE DASHBOARD</title>
    <link rel="stylesheet" type="text/css" href="tech/baseline.css">

    <style>
        body {
            background-image: url(images/backdrop.jpg);
            background-position: center center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            background-color: #464646;
        }
    </style>
</head>
<body>



<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 12/09/2018
 * Time: 15:37
 */
include 'tech/connect.php';
include('tech/navbar/navbarca.php');
include 'tech/divs.php';

$query_text = "SELECT users.id, user_preferences.contents, users.first_name as nome, users.last_name as cognome, users.is_admin as admin, user_preferences.date_modified as data_mod, user_preferences.contents as contents
                              FROM user_preferences, users WHERE
	                          category = 'home' AND
	                          user_preferences.assigned_user_id = users.id AND
                              users.deleted = 0 AND
                              users.employee_status != 'terminated'";




echo '

<div class="titolo">COPIA DASHBOARD</div><P></P>

<form name="copia_dashboard" method="post" onsubmit="return confirm(\'Vuoi sovrascrivere la dashboard utente con il modello selezionato?\');">
<table class="bluetable" style="width:60%">
    <thead>
    <tr align="center"><th colspan="2">Scegli il template di origine:</th><th colspan="2">Scegli l\'utente di destinazione</th> </tr></thead>
    <tr><td>Template</td>
            <td>
                <select name="id_origine">';

$array_dash1 = $conn_crm->query($query_text." and first_name = 'template'"); //??
while ($select_data = $array_dash1->fetch_assoc()) {
   echo '<option value="'.$select_data["id"].'">'.$select_data["nome"].' '.$select_data["cognome"].'</option>';
}

    echo '</select>
</td><td>Utente | Data modifica Dashboard</td>
<td><select name="id_destinazione">';

$array_dash2 = $conn_crm->query($query_text ." and first_name != 'template'"); //??
while ($select_data = $array_dash2->fetch_assoc()) {
    echo '<option value="'.$select_data["id"].'">'.$select_data["nome"].' '.$select_data["cognome"].' | '.date('d/m/Y' , strtotime($select_data['data_mod'])).'</option>';
}

echo '</select></td></tr>
<tr><td colspan="4" style="color:black;text-align:center"><input type="submit" name="copia_dash" value="COPIA"></TD></TR>


</table></form>

<hr>

<div class="titolo">ELENCO DELLE DASHBOARD UTENTE</div><P></P>';

$array_dash = $conn_crm->query($query_text." and first_name != 'template'");


echo '<table class="blueTable" style="width:80%">
            <thead><tr><th>ID ERP</th><th>NOME</th><th>COGNOME</th><th>ADMIN</th><th>ULTIMA MODIFICA</th><Th colspan="2">CONTENT</Th></tr></thead>';

while ($data_dash = $array_dash->fetch_assoc()) {

    echo "<tr>
            <td width='15%' ><div style='font-size: x-small'> ".$data_dash['id']."</div></td>
            <td width='15%'>".$data_dash['nome']."</td>
            <td width='15%'>".$data_dash['cognome']."</td>
            <td width='5%'><div style='text-align:center'>";
            if ($data_dash['admin']==1) {echo '<img src="images/tick_green.png" height="25">';}
    echo "</div></td>
            <td width='15%'>". date('d/m/Y H:m',strtotime($data_dash['data_mod']))."</td>
            <td width='30%'><div style='word-break:break-all; height:30px; overflow: hidden; font-size: xx-small'>".$data_dash['contents']."</div></td>
            <td width = '5%' align='center'><a href='#' title='Salvataggi utente' id='saves_link".$data_dash['id']."' name='saves_link".$data_dash['id']."'><img src='images/recover.png' width='25'></a></td>
          </tr>";
//recover_list.php?id_erp=".$data_dash['id']."
    echo "
        <script type='text/javascript'>
            $('#saves_link".$data_dash['id']."').click(function(e) {
            e.preventDefault();
            //console.log('TEST!');
            $('#saves_list').load('recover_list.php?id_erp=".$data_dash["id"]."').modal('show');
            });
        </script>
";
}

echo '</table>';

//azioni su post
if (isset($_POST['copia_dash'])) {

    $array_db = $conn_crm->query($query_text. " and users.id = '".$_POST['id_destinazione']."'")->fetch_assoc(); //recupero i dati della dashboard origine
    $user = $array_db['nome'] . ' ' . $array_db['cognome'];
    //inserisco backup
    $conn_agent->query("INSERT INTO db_savefiles  (id_erp, user, contents, save_date) VALUES ('".$array_db['id']."','".$user."','".$array_db['contents']."','".$array_db['data_mod']."')");
    //aggiorno la dashboard
/*    $contents2copy = $conn_crm->query("SELECT contents FROM user_preferences WHERE assigned_user_id = '".$_POST['id_origine']."' AND category = 'home' ")->fetch_assoc();
    $conn_crm->query("UPDATE user_preferences SET contents = '".$contents2copy['contents']."' where assigned_user_id = '".$_POST['id_destinazione']."' AND category = 'home'");*/

echo '

<script type="text/javascript">
        $(document).ready(function(){
        $(\'#copiaok_modal\').modal(\'show\');
        });
</script>


';

}


















