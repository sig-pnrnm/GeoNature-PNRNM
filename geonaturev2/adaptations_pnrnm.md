# Personnalisation de GeoNature v2 au PNR Normandie-Maine
suite à l'installation d'une VM vierge avec le script `install_all.sh`


- [x] Changer le nom de l'application ([cf. : #1](adaptations_pnrnm.md#1--nom-de-lapplication-et-autres-paramètres) )
- [x] Modifier le texte d'accueil ([cf. : #2](adaptations_pnrnm.md#2--modifier-texte-daccueil-) )
- [x] Modifier les logos
- [ ] Créer une connexion (FDW) à la BDD de GeoNature v1
- [ ] Récupérer les observateurs de GeoNature V1 (script de migration v1>V2 ?)
- [ ] Récupérer les observations de GeoNature V1 (script de migration v1>V2 ?)
- [ ] Récupérer les observateurs + observations + ... (?) de SERENA


## 1 # Nom de l'application et autres paramètres

Editer le fichier `/config/geonature_config.toml` en s'inspirant de `default_config.toml.example`

Dans le cas de l'instance Parc :
```
# GeoNature backend global configuration file
# Don't change this

# Database
SQLALCHEMY_DATABASE_URI = "postgresql://geonatuser:xxxmotdepassexxxx@localhost:5432/geonaturedb"
URL_APPLICATION = 'http://biodiversite.parc-naturel-normandie-maine.fr/geonature' 
API_ENDPOINT = 'http://biodiversite.parc-naturel-normandie-maine.fr/geonature/api'
API_TAXHUB = 'http://biodiversite.parc-naturel-normandie-maine.fr/taxhub/api'

# Application
appName = 'GeoNature 2 - Normandie-Maine'   
SECRET_KEY = 'super secret key'

LOCAL_SRID = '2154'

DEFAULT_LANGUAGE='fr'
```


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
      <h3 style="background-color: rgba(255, 255, 255, 0.5); border-radius: 25px;"> GeoNature v2</h3>
      <img src="assets/images/LogoGeonature.png" alt="GeoNature v2">
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

ça se passe là pour les logos :

```html
/home/geonatureadmin/geonature/frontend/src/custom/images
/home/geonatureadmin/geonature/frontend/src/assets/images
```
et non là :

~~/home/geonatureadmin/geonature/frontend/dist/custom/images~~

~~/home/geonatureadmin/geonature/frontend/dist/assets/images~~


(logo_structure.png = 50 x 46 px)


Pour la favicon :
```html
/home/geonatureadmin/geonature/frontend/src
```
