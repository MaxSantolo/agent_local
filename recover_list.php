<head>
    <title>SALVATAGGI UTENTE</title>
    <link rel="stylesheet" type="text/css" href="tech/baseline.css">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

</head>
<body>


<?php
/**
 * Created by PhpStorm.
 * User: msantolo
 * Date: 17/09/2018
 * Time: 08:58
 */
include 'tech/connect.php';
//include 'tech/divs.php';

//include('tech/navbar/navbarca.php');

/*echo '<div class="modal fade" id="saves_list" tabindex="-1" role="dialog" aria-labelledby="saveslabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="myModalLabel">Elenco dei salvataggi</h4>
            </div>
            <div class="modal-body"><P>';*/

echo '<div class="modal-dialog" style="background-color: rgba(255, 255, 255, 0.7)">';
if (isset($_GET['id_erp'])) {
    $id_erp = $_GET['id_erp'];
    $db_saves_array = $conn_agent->query("SELECT * FROM db_savefiles WHERE id_erp = '".$id_erp."'");

    if ($db_saves_array->num_rows>0) {

    echo '<table class="blueTable" style="width:80%"><div class="titolo">ELENCO SALVATAGGI UTENTE</div><P></P>
            <thead><tr><th>UTENTE</th><th>DATA VERSIONE</th><Th colspan="3">CONTENT</Th></tr></thead>';


    while ($save_row = $db_saves_array->fetch_assoc()) {

            echo "<tr>
            <td width='39%'>".$save_row['user']."</td>
            <td width='10%'>". date('d/m/Y H:m',strtotime($save_row['save_date']))."</td>
            <td width='45%'><div style='word-break:break-all; height:30px; overflow: hidden; font-size: xx-small'>".$save_row['contents']."</div></td>
            <td width='3%'><a href='tech/functions.php?fx=delete&id_save=".$save_row['id']."' title='Cancella' onclick=\"return confirm('Elimino il salvataggio corrente?')\"><div style='text-align:center'><img src='images/delete.png' width='25'></div></a></td>
            <td width='3%'><a href='tech/functions.php?fx=restore&id_erp=".$save_row['id_erp']."' title='Recupera' onclick=\"return confirm('Recupero il salvataggio corrente?')\"><div style='text-align:center'><img src='images/restore.png' width='25'></div></a></td>
          </tr>";

        }

        echo '</table>'; } else { echo '<div class="titolo">NESSUN SALVATAGGIO PRECEDENTE</div><P></P>';}

}
echo '</body>';
echo '
<P><div class="modal-footer">
         <button type="button" class="" data-dismiss="">Chiudi</button>
      </div>
</div>';

      /*     </P> </div>
        </div>
    </div>
</div>';*/