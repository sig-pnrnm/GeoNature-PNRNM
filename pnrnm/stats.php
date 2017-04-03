<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>Stats Sources</title>
    <style type="text/css">
        html, body { margin: 5px; background-color: #3D3C3A;}
        #header { width: 100%; height: 60px; color: red; font-size:2em; background-image: url(../geonature/images/bandeau_geonature.jpg); border-radius: 10px;}
    </style>
	</head>
<?php

require("connecter.php");

//les requêtes
$requete_sources = "WITH 	lastweek as (	SELECT	count(s.id_synthese) as nb_lastweek, b.nom_source
			FROM	synthese.syntheseff s
			JOIN	synthese.bib_sources b ON s.id_source = b.id_source
			WHERE ((date(s.dateobs) >= date(NOW()) - 7) OR (date(s.date_update) >= date(NOW()) - 7))
			GROUP BY b.nom_source ),
	lastmonth as (	SELECT	count(s.id_synthese) as nb_lastmonth, b.nom_source
			FROM	synthese.syntheseff s
			JOIN	synthese.bib_sources b ON s.id_source = b.id_source
			WHERE ((date(s.dateobs) >= date(NOW()) - 31) OR (date(s.date_update) >= date(NOW()) - 31))
			GROUP BY b.nom_source )

SELECT	b.nom_source, count(s.id_synthese) as nb_tot, nb_lastweek, nb_lastmonth
FROM	synthese.syntheseff s
JOIN	synthese.bib_sources b ON s.id_source = b.id_source
LEFT JOIN	lastweek lw ON b.nom_source = lw.nom_source
LEFT JOIN	lastmonth lm ON b.nom_source = lm.nom_source
GROUP BY b.nom_source, nb_lastweek, nb_lastmonth
ORDER BY nb_tot DESC";

$requete_especes_7j = "Select bl.nom_liste, '../geonature/'|| bl.picto as urlpicto, CASE WHEN tx.nom_vern IS NULL THEN '[pas de nom français]' ELSE split_part(tx.nom_vern, ', ',1) END as nom_fr, tx.lb_nom as nom_la, string_agg(DISTINCT s.observateurs, ' / ') as observateurs, count(id_synthese) as nbobs, '../atlas/espece/'||bn.cd_nom as urlespece
  FROM synthese.syntheseff s
  JOIN taxonomie.taxref tx ON tx.cd_nom = s.cd_nom
  JOIN taxonomie.bib_noms bn ON tx.cd_ref = bn.cd_nom
  JOIN taxonomie.cor_nom_liste cnl ON cnl.id_nom = bn.id_nom
  JOIN taxonomie.bib_listes bl ON cnl.id_liste = bl.id_liste
  WHERE ((date(dateobs) >= date(NOW()) - 7) OR (date(s.date_update) >= date(NOW()) - 7)) AND bl.id_liste < 100
  GROUP BY nom_liste, urlpicto, nom_fr, nom_la, urlespece
  ORDER BY nom_liste, nom_fr ASC";
  
$requete_observateurs_7j = "SELECT unnest(string_to_array(observateurs,', ')) as obs, count(id_synthese) as nbobs, count(DISTINCT cd_nom) as nbsps
  FROM synthese.syntheseff
  WHERE date(dateobs) >= date(NOW()) - 7
  GROUP BY obs
  ORDER BY nbobs DESC";

$requete_observateurs_1mois = "SELECT unnest(string_to_array(observateurs,', ')) as obs, count(id_synthese) as nbobs, count(DISTINCT cd_nom) as nbsps
  FROM synthese.syntheseff
  WHERE date(dateobs) >= date(NOW()) - 31
  GROUP BY obs
  ORDER BY nbobs DESC";

$requete_observateurs_total_parc = "SELECT obs, nbobs, nbsps FROM synthese.v_stats_observateurs o
JOIN utilisateurs.t_roles r ON O.obs = (r.nom_role || ' ' || r.prenom_role)
WHERE r.id_organisme = 2
LIMIT 10";


//exécution des requêtes
$result_requete_sources = pg_query($requete_sources) or die ("Erreur requête") ;
$result_requete_especes_7j = pg_query($requete_especes_7j) or die ("Erreur requête") ;
$result_observateurs_7j = pg_query($requete_observateurs_7j) or die ("Erreur requête") ;
$result_observateurs_1mois = pg_query($requete_observateurs_1mois) or die ("Erreur requête") ;
$result_observateurs_total_parc = pg_query($requete_observateurs_total_parc) or die ("Erreur requête") ;
?>

<script type="text/javascript">
var refresh = window.getElementById('refresh');
refresh.addEventListener('click', location.reload(), false);
</script>

	<body>
	<div id="header"></div>
		<div style="color:grey; font-size:1.2em;"><i>Statistiques mises à jour le <?php setlocale (LC_TIME, 'fr_FR.utf8','fra'); echo (strftime("%A %d %B %G")); ?> à <?php echo date('H:i'); ?></i></div>
		<div style="color:orange; font-size:1.5em;">Nombre de données par Protocoles</div>

<?php         if($result_requete_sources) {
            echo '<table bgcolor="white"'."\n";
            echo '<tr style="text-align: center">';
            echo '<td bgcolor="#006498"><font color="white"><b>Protocole</b></font></td>';
            echo '<td bgcolor="#006498" width="100px"><font color="white"><b>Total</b></font></td>';
            echo '<td bgcolor="#006498" width="100px"><font color="white"><b>J-7</b></font></td>';
            echo '<td bgcolor="#006498" width="100px"><font color="white"><b>J-31</b></font></td>';
            echo '</tr>'."\n";
        
        
        //
            while($row = pg_fetch_array($result_requete_sources)) {
                echo '<tr>';
                echo '<td bgcolor="#dddddd" style="text-align: left">'.$row["nom_source"].'</td>';
                echo '<td bgcolor="#dddddd" style="text-align: right">'.number_format($row["nb_tot"],0,',',' ').'</td>';
                echo '<td bgcolor="#dddddd" style="text-align: right">'.number_format($row["nb_lastweek"],0,',',' ').'</td>';
                echo '<td bgcolor="#dddddd" style="text-align: right">'.number_format($row["nb_lastmonth"],0,',',' ').'</td>';
                echo '</tr>'."\n";
            }
            echo '</table>'."\n";     
            echo '<br>'."\n";
        }
        else {
              echo "Une erreur s'est produite.\n";
            echo "Pas d\'enregistrements dans cette table...";
              exit;
              }
?>

		<div style="color:orange; font-size:1.5em;">Stats par Observateurs<br>
		<input type="button" value="Rafraichir" id="refresh" /></div>
    <table style="text-align: left;" border="0"
     cellpadding="0" cellspacing="0">
      <tbody>
        <tr>
          <td valign="top"><font color="white"><b>Semaine dernière :</b></font><br>
              <?php         if($result_observateurs_7j) {
            echo '<table bgcolor="white"'."\n";
            echo '<tr style="text-align: center">';
            echo '<td bgcolor="#006498"><font color="white"><b>Observateurs</b></font></td>';
            echo '<td bgcolor="#006498" width="100px"><font color="white"><b>Observations</b></font></td>';
            echo '<td bgcolor="#006498" width="100px"><font color="white"><b>Espèces</b></font></td>';
            echo '</tr>'."\n";
        
        
        //
            while($row = pg_fetch_array($result_observateurs_7j)) {
                echo '<tr>';
                echo '<td bgcolor="#dddddd" style="text-align: right">'.$row["obs"].'</td>';
                echo '<td bgcolor="#dddddd" style="text-align: right">'.number_format($row["nbobs"],0,',',' ').'</td>';
                echo '<td bgcolor="#dddddd" style="text-align: right">'.number_format($row["nbsps"],0,',',' ').'</td>';
                echo '</tr>'."\n";
            }
            echo '</table>'."\n";     
            echo '<br>'."\n";
        }
        else {
              echo "Une erreur s'est produite.\n";
            echo "Pas d\'enregistrements dans cette table...";
              exit;
              }
              ?>
          </td>
          <td width="10px" bgcolor="#3D3C3A"><br></td>
          <td valign="top"><font color="white"><b>Mois dernier :</b></font><br>
              <?php         if($result_observateurs_1mois) {
            echo '<table bgcolor="white"'."\n";
            echo '<tr style="text-align: center">';
            echo '<td bgcolor="#006498"><font color="white"><b>Observateurs</b></font></td>';
            echo '<td bgcolor="#006498" width="100px"><font color="white"><b>Observations</b></font></td>';
            echo '<td bgcolor="#006498" width="100px"><font color="white"><b>Espèces</b></font></td>';
            echo '</tr>'."\n";
        
        
        //
            while($row = pg_fetch_array($result_observateurs_1mois)) {
                echo '<tr>';
                echo '<td bgcolor="#dddddd" style="text-align: right">'.$row["obs"].'</td>';
                echo '<td bgcolor="#dddddd" style="text-align: right">'.number_format($row["nbobs"],0,',',' ').'</td>';
                echo '<td bgcolor="#dddddd" style="text-align: right">'.number_format($row["nbsps"],0,',',' ').'</td>';
                echo '</tr>'."\n";
            }
            echo '</table>'."\n";     
            echo '<br>'."\n";
        }
        else {
              echo "Une erreur s'est produite.\n";
            echo "Pas d\'enregistrements dans cette table...";
              exit;
              }
              ?></td>
          <td width="10px" bgcolor="#3D3C3A"><br></td>
          <td valign="top"><font color="white"><b>Total GeoNature :</b></font><br>
              <?php         if($result_observateurs_total_parc) {
            echo '<table bgcolor="white"'."\n";
            echo '<tr style="text-align: center">';
            echo '<td bgcolor="#006498"><font color="white"><b>Observateurs</b></font></td>';
            echo '<td bgcolor="#006498" width="100px"><font color="white"><b>Observations</b></font></td>';
            echo '<td bgcolor="#006498" width="100px"><font color="white"><b>Espèces</b></font></td>';
            echo '</tr>'."\n";
        
        
        //
            while($row = pg_fetch_array($result_observateurs_total_parc)) {
                echo '<tr>';
                echo '<td bgcolor="#dddddd" style="text-align: right">'.$row["obs"].'</td>';
                echo '<td bgcolor="#dddddd" style="text-align: right">'.number_format($row["nbobs"],0,',',' ').'</td>';
                echo '<td bgcolor="#dddddd" style="text-align: right">'.number_format($row["nbsps"],0,',',' ').'</td>';
                echo '</tr>'."\n";
            }
            echo '</table>'."\n";     
            echo '<br>'."\n";
        }
        else {
              echo "Une erreur s'est produite.\n";
            echo "Pas d\'enregistrements dans cette table...";
              exit;
              }
              ?></td>
        </tr>
      </tbody>
    </table>
    <br>
		<div style="color:orange; font-size:1.5em;">Espèces observées ou saisies les 7 derniers jours</div>
		
		
<?php         if($result_requete_especes_7j) {
            echo '<table bgcolor="white"'."\n";
            echo '<tr style="text-align: center">';
            echo '<td bgcolor="#006498"><font color="white"> </font></td>';
            echo '<td bgcolor="#006498"><font color="white"><b>Nom français</b></font></td>';
            echo '<td bgcolor="#006498"><font color="white"><b>Nom latin</b></font></td>';
            echo '<td bgcolor="#006498"><font color="white"><b>Observateurs</b></font></td>';
            echo '<td bgcolor="#006498"><font color="white"><b>Nb obs</b></font></td>';
            echo '<td bgcolor="#006498"><font color="white"><b>Atlas</b></font></td>';
            echo '</tr>'."\n";
        
        //
            while($row = pg_fetch_array($result_requete_especes_7j)) {
                echo '<tr>';
                echo '<td bgcolor="#dddddd"><img alt="'.$row["nom_liste"].' " title="'.$row["nom_liste"].'" src="'.$row["urlpicto"].'"></td>';
                echo '<td bgcolor="#dddddd" style="text-align: left">'.$row["nom_fr"].'</td>';
                echo '<td bgcolor="#dddddd" style="text-align: left"><i>'.$row["nom_la"].'</i></td>';
                echo '<td bgcolor="#dddddd" style="text-align: left"><small>'.$row["observateurs"].'</small></td>';
                echo '<td bgcolor="#dddddd" style="text-align: right">'.$row["nbobs"].'</td>';
                echo '<td bgcolor="#dddddd" style="text-align: right"><a href="'.$row["urlespece"].'">Fiche</a></td>';
                echo '</tr>'."\n";
            }
            echo '</table>'."\n";     
            echo '<br>'."\n";
        }
        else {
              echo "Une erreur s'est produite.\n";
            echo "Pas d\'enregistrements dans cette table...";
              exit;
              }
?>

	</body>
</html>
