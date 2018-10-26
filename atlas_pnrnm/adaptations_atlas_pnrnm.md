# Quelques adaptations de l'Atlas du Parc Normandie-Maine


## Ajout des Villes portes à la carte

Le territoire du Parc est complété par les 14 villes-portes adhérentes à la Charte.

Postérieurement à l'installation initiale (décembre 2016) le JSON du territoire (`/home/geonatureadmin/atlas/static/custom/territoire.json`) est modifié en ajoutant les villes portes.
(il aurait été préférable de gérer initialement le territoire à l'installation de GeoNature-Atlas)


### Création du JSON
La modification a été faite sous QGis, puis export en GeoJson.

(ToDo : simplifier les géométries - en gardant la topologie ! - pour un chargement + rapide. JSON = 2.1 Mo).

### Mise en forme du JSON
Le JSON contient une propriete `nom` (valeurs : `Parc Normandie-Maine` et `Villes Portes`) sur laquelle on base un style dynamique en modifiant le fichier `/home/geonatureadmin/atlas/static/mapGenerator.js`
Le style est créé par cette fonction :

```javascript
style: function(feature) {
	   n = feature.properties.nom;
	   return n == 'Parc Normandie-Maine' ? {color: '#3388ff', opacity:1, fill: false, weight: 3} :
				  {color: '#3388ff', opacity:1, fill: false, weight: 2, dashArray: '3'};
	 }
```

### Ajouter une légende

Modification du contenu de la variable `htmlLegend` dans les fichiers `static/mapMailles.js`, `static/mapCommune.js`, `static/mapPoint.js`, `static/mapSwitcher.js` et `static/mapHome.js` 

- Dans `mapHome.js` :
```javascript
htmlLegend = 	"<p><i style='border: solid 1px red; width: 30px;'> &nbsp; &nbsp; &nbsp;</i> <span> Maille comportant au moins une observation </span></p>" +
				"<p><i style='border:3px solid #3388ff; width: 30px;'> &nbsp; &nbsp; &nbsp;</i> <span> Périmètre du Parc Normandie-Maine </span></p>" +
				"<p><i style='border:2px dashed #3388ff; width: 30px;'> &nbsp; &nbsp; &nbsp;</i> <span> Villes Portes </span></p>";
```

- dans toues les autres :
```javascript
htmlLegend =	"<p><i style='border:3px solid #3388ff; width: 30px;'> &nbsp; &nbsp; &nbsp;</i> <span> Périmètre du Parc Normandie-Maine </span></p>" +
				"<p><i style='border:2px dashed #3388ff; width: 30px;'> &nbsp; &nbsp; &nbsp;</i> <span> Villes Portes </span></p>";
```

### Mise à jour du territoire en Base de données

( A FAIRE ! )

