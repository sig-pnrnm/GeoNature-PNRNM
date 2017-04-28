<?php
/*
 * Title:   PostGIS to GeoJSON
 * Notes:   Query a PostGIS table or view and return the results in GeoJSON format, suitable for use in OpenLayers, Leaflet, etc.
 * Author:  Bryan R. McBride, GISP
 * Contact: bryanmcbride.com
 * GitHub:  https://github.com/bmcbride/PHP-Database-GeoJSON
 */
# Connect to PostgreSQL database
$conn = new PDO('pgsql:host=localhost;dbname=geonaturedb','*****','*****');
# Build SQL SELECT statement and return the geometry as a GeoJSON element
$sql = "SELECT  bl.nom_liste,
                'http://149.202.129.102/geonature/'|| bl.picto as urlpicto,
                tx.lb_nom as nom_la,
                CASE WHEN tx.nom_vern IS NULL THEN null ELSE split_part(tx.nom_vern, ', ',1) END as nom_fr,
                'http://149.202.129.102/atlas/espece/'||bn.cd_nom as urlespece,
                s.dateobs,
                s.observateurs,
                s.the_geom_2154,
                ST_AsGeoJSON(ST_Transform((the_geom_2154),4326),6) AS geojson
        FROM synthese.syntheseff s
        JOIN taxonomie.taxref tx ON tx.cd_nom = s.cd_nom
        JOIN taxonomie.bib_noms bn ON tx.cd_ref = bn.cd_nom
        JOIN taxonomie.cor_nom_liste cnl ON cnl.id_nom = bn.id_nom
        JOIN taxonomie.bib_listes bl ON cnl.id_liste = bl.id_liste
        WHERE ((date(s.dateobs) >= date(NOW()) - 7) OR (date(s.date_update) >= date(NOW()) - 7)) AND bl.id_liste < 1000";
/*
* If bbox variable is set, only return records that are within the bounding box
* bbox should be a string in the form of 'southwest_lng,southwest_lat,northeast_lng,northeast_lat'
* Leaflet: map.getBounds().toBBoxString()
*/
if (isset($_GET['bbox'])) {
    $bbox = explode(',', $_GET['bbox']);
    $sql = $sql . ' WHERE ST_Transform(the_geom_2154, 4326) && ST_SetSRID(ST_MakeBox2D(ST_Point('.$bbox[0].', '.$bbox[1].'), ST_Point('.$bbox[2].', '.$bbox[3].')),4326);';
}
# Try query or error
$rs = $conn->query($sql);
if (!$rs) {
    echo 'An SQL error occured.\n';
    exit;
}
# Build GeoJSON feature collection array
$geojson = array(
   'type'      => 'FeatureCollection',
   'features'  => array()
);
# Loop through rows to build feature arrays
while ($row = $rs->fetch(PDO::FETCH_ASSOC)) {
    $properties = $row;
    # Remove geojson and geometry fields from properties
    unset($properties['geojson']);
    unset($properties['the_geom_2154']);
    $feature = array(
         'type' => 'Feature',
         'geometry' => json_decode($row['geojson'], true),
         'properties' => $properties
    );
    # Add feature arrays to feature collection array
    array_push($geojson['features'], $feature);
}
header('Content-type: application/json');
echo json_encode($geojson, JSON_NUMERIC_CHECK);
$conn = NULL;
?>
