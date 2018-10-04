# 1 # Nom de l'application

Editer le fichier `/config/geonature_config.toml` en s'inspirant de `default_config.toml.example`
Puis lancer les commandes suivantes, depuis `/home/geonatureadmin/geonature/backend`
```
source venv/bin/activate
geonature update_configuration
deactivate
```

(ça prend un peu de temps !)


# 2 # Modifier texte d'accueil :

Ca se passe ici : `/home/geonatureadmin/geonature/frontend/src/custom/components/introduction/introduction.component.html`

Modification (ajout) :
```
        <h2 class="underlined main-color"> Bienvenue dans GeoNature V2 </h2>

        <p>
          Vous pouvez tester cette interface de démonstration de GeoNature v2 pour découvrir les nouvelles fonctionnalités.<br>
        </p>
        
        <div class="alert alert-danger">
          <strong>Serveur de tests avant mise en production prévue début 2019</strong><br>
          <span class="glyphicon glyphicon-warning-sign"></span> <strong>Attention !</strong> Les données saisies ici seront effacées régulièrement.<br>
          Pour saisir des observations réelles, continuez d'utiliser l'interface GeoNature v1 à <a href="http://observatoire.parc-naturel-normandie-maine.fr/geonature/">cette adresse</a>.
        </div>
        <p>
```


/home/geonatureadmin/geonature/frontend/src/app/components/login/login.component.html
```
	<div id="loading_gnv2">
	</div>
```

/home/geonatureadmin/geonature/frontend/dist/custom/custom.scss

```
	#loading_gnv2{
	  position: absolute;
	  left: 0;
	  top: 0;
	  width: 100%;
	  height: 100%;
	  z-index: 20000;
	  background: url(custom/images/geonature_background.jpg) center 10% no-repeat #ecd9eb;
	  background-size: 100%;
	  display: flex;
	  flex-direction: column;
	  align-items: center;
	  justify-content: center;
	}
```

/home/geonatureadmin/geonature/frontend/src/app/components/sidenav-items/sidenav-items.component.html
```
  <p class="p-small">
    Hébergé et mis à disposition par le 
    <a target="_blank" href="http://www.parc-naturel-normandie-maine.fr"> PNR Normandie-Maine</a>
  </p>
```





refaire le "update configuration" comme dans ## 1 #


# 3 # Customiser l'interface (logos)

ça se passe là :
`/home/geonatureadmin/geonature/frontend/dist/custom/images`
`/home/geonatureadmin/geonature/frontend/dist/assets/images`
(logo_structure.png = 50 x 46 px)


