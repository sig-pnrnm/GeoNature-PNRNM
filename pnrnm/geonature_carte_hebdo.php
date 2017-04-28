<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css"/>
<link rel="stylesheet" href="markercluster/MarkerCluster.css" />
<link rel="stylesheet" href="markercluster/MarkerCluster.Default.css" />
<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>
<script src="http://code.jquery.com/jquery-1.11.3.min.js"></script>
<script src="markercluster/leaflet.markercluster-src.js"></script>
</head>
<body>
<div style="position: absolute; top: 20px; left: 55px; z-index: 10; padding: 10px;">
      <b>Saisie GeoNature PNRNM des 7 derniers jours</b><br>
      <i> <?php $date1 = date("d/m/Y", time()-7*24*3600);
          $date2 = date("d/m/Y");
          echo "Du ".$date1." au ".$date2;?><br>
          données mises à jour le <?php setlocale (LC_TIME, 'fr_FR.utf8','fra'); echo (strftime("%A %d %B %G")); ?> à <?php echo date('H:i'); ?></i>
      <div style="	background-color: #E8E4BF; position:absolute; z-index:-1; top:0; left:0; right:0; bottom:0; opacity:0.8; border-radius: 10px;"></div>
</div>
<div id="map" style="width: 100%; height: 100%;"></div>
<script type="text/javascript">


var tiles = L.tileLayer('http://b.tiles.mapbox.com/v3/landplanner.map-xswoybbb/{z}/{x}/{y}.jpg', {
			maxZoom: 15,
			attribution: '&copy; Contributeurs <a href="http://osm.org/copyright">OpenStreetMap</a> | &copy; MapBox'
		});
var map = L.map('map')
				.addLayer(tiles);

map.setView([48.56317164, -0.15068650], 9);

function addObsToMap(obs, map) {
var markers = L.markerClusterGroup();
		var obslayer = L.geoJson(obs, {
			onEachFeature: function (feature, layer) {
            var popupText = "<img alt='" + feature.properties.nom_liste + "' src='" + feature.properties.urlpicto + "'> " + "<a href='" + feature.properties.urlespece + "'>" + feature.properties.nom_fr + " (<i>" + feature.properties.nom_la + "</i>)</a>" + "<br>Date: " + feature.properties.dateobs + "<br>Observateurs: " + feature.properties.observateurs;
            layer.bindPopup(popupText); }
		});
		markers.addLayer(obslayer);
		map.addLayer(markers);
		map.fitBounds(markers.getBounds());
}

var stylepnrnm = {
        fillColor: 'green',
        opacity: 0.8,
        color: 'white',  //Outline color
        weight: 2,
        dashArray: "5 5",
        fillOpacity: 0.1
};

function addPnrnmToMap(pnrnm, map) {
		var pnrnmlayer = L.geoJson(pnrnm, {
			style: stylepnrnm,
			onEachFeature: function (feature, layer) {
            var popupText = feature.properties.nomzone;
            layer.bindPopup(popupText); }
		});
		map.addLayer(pnrnmlayer);
}


$.getJSON("geojson_7j.php", function(obs) { addObsToMap(obs, map); });

$.getJSON("geojson_pnrnm.php", function(pnrnm) { addPnrnmToMap(pnrnm, map); });


</script>
</body>
</html>
