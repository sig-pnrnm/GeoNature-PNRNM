# Quelques adaptations de l'Atlas du Parc Normandie-Maine


## Ajout des Villes portes à la carte

Le territoire du Parc est complété par les 14 villes-portes adhérentes à la Charte.

Postérieurement à l'installation initiale (décembre 2016) le JSON du territoire (`/home/geonatureadmin/atlas/static/custom/territoire.json`) est modifié en ajoutant les villes portes.

(le tavail en BDD sera fait ulterieurement pour les données)

La modification a été faite sous QGis, puis export en GeoJson.

Le JSON contient une propriete `nom` (valeurs : `Parc Normandie-Maine` et `Villes Portes`) sur laquelle on base un style dynamique en modifiant le fichier `/home/geonatureadmin/atlas/static/mapGenerator.js`
Le style est créé par cette fonction :

```javascript
style: function(feature) {
	   n = feature.properties.nom;
	   return n == 'Parc Normandie-Maine' ? {color: '#3388ff', opacity:1, fill: false, weight: 3} :
				  {color: '#3388ff', opacity:1, fill: false, weight: 2, dashArray: '3'};
	 }
```
