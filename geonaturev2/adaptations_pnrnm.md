# Personnalisation de GeoNature v2 au PNR Normandie-Maine
suite à l'installation d'une VM vierge avec le script `install_all.sh`


- [x] Changer le nom de l'application ([cf. : #1](adaptations_pnrnm.md#1--nom-de-lapplication) )
- [x] Modifier le texte d'accueil ([cf. : #2](adaptations_pnrnm.md#2--modifier-texte-daccueil-) )
- [x] Modifier les logos
- [ ] Créer une connexion (FDW) à la BDD de GeoNature v1
- [ ] Récupérer les observateurs de GeoNature V1 (script de migration v1>V2 ?)
- [ ] Récupérer les observations de GeoNature V1 (script de migration v1>V2 ?)
- [ ] Récupérer les observateurs + observations + ... (?) de SERENA


## 1 # Nom de l'application

Editer le fichier `/config/geonature_config.toml` en s'inspirant de `default_config.toml.example`
Puis lancer les commandes suivantes, depuis `/home/geonatureadmin/geonature/backend`
```
source venv/bin/activate
geonature update_configuration
deactivate
```

(ça prend un peu de temps !)


## 2 # Modifier texte d'accueil :

Ca se passe ici : `/home/geonatureadmin/geonature/frontend/src/custom/components/introduction/introduction.component.html`

Modification (ajout) :
```html
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

```html
	<div id="loading_gnv2">
	</div>
```

Ecran de connexion (ajout d'un background) :
/home/geonatureadmin/geonature/frontend/src/index.html
```html
<style type="text/css">body { 
    margin:0;
    padding:0;
    background: url(assets/images/geonature_background.jpg) no-repeat center fixed; 
    -webkit-background-size: cover; /* pour anciens Chrome et Safari */
    background-size: cover; /* version standardisée */
  }
</style>
```

/home/geonatureadmin/geonature/frontend/src/app/components/sidenav-items/sidenav-items.component.html
```html
  <p class="p-small">
    Hébergé et mis à disposition par le 
    <a target="_blank" href="http://www.parc-naturel-normandie-maine.fr"> PNR Normandie-Maine</a>
  </p>
```

Une fois les modifications terminées, lancer `npm run build` depuis le répertoire geonature/frontend



## 3 # Customiser l'interface (logos)

ça se passe là :

```html
/home/geonatureadmin/geonature/frontend/src/custom/images
/home/geonatureadmin/geonature/frontend/src/assets/images
```
et non là :

~~/home/geonatureadmin/geonature/frontend/dist/custom/images~~

~~/home/geonatureadmin/geonature/frontend/dist/assets/images~~

(logo_structure.png = 50 x 46 px)


