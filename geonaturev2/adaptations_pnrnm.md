# Personnalisation de GeoNature v2 au PNR Normandie-Maine

suite à l'installation d'une VM vierge avec le script `install_all.sh` ([Procédure](https://geonature.readthedocs.io/fr/latest/installation-all.html#installation-de-l-application) )


- [x] Changer le nom de l'application ([cf. : #1](adaptations_pnrnm.md#1--nom-de-lapplication-et-autres-paramètres) )
- [x] Modifier le texte d'accueil ([cf. : #2](adaptations_pnrnm.md#2--modifier-texte-daccueil-) )
- [x] Modifier les logos
- [x] Connexion (FDW) à la BDD de GeoNature v1 ([cf. : #4](adaptations_pnrnm.md#4--connexion-fdw-à-geonature-v1) )
- [x] Récupérer les observateurs de GeoNature V1 (script de migration v1>V2 ?)
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


## X # Customiser les fonds de cartes

### Récupérer une clé IGN pour le serveur

ça se passe ici : http://professionnels.ign.fr/ign/contrats
chosir le type de sécurisation "Referer ou IP"
et récupérer la clé IGN après avoir finalisé la commande

Clé VM "Biodiversité" du PNRNM : `oasdnxc81wien8vrs194j0jt`

### Récupérer une clé pour l'API MapBox

Connecté à MapBox, ça se passe ici : https://www.mapbox.com/account/access-tokens
Token GeoNature V2 : `pk.eyJ1IjoicG5yLW5vcm1hbmRpZS1tYWluZSIsImEiOiJjam5idW02amEwMTJmM3FudmRjdDJqanJpIn0.UXJfuDjS8aLtDwXhyw5yOg`

### Editer le fichier `map.config.ts`

Le fichier se situe ici :  `/home/geonatureadmin/geonature/frontend/src/conf/map.config.ts`

Ajout d'un layer OSM N&B :
```javascript
    {name: 'OpenStreetMap NB',
    layer: 'http://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}.png',
    attribution: '&copy; OpenStreetMap -  &copy; CartoDB'
    },
	{name: 'Carte IGN',
    layer: 'https://gpp3-wxs.ign.fr/oasdnxc81wien8vrs194j0jt/geoportail/wmts?LAYER=GEOGRAPHICALGRIDSYSTEMS.MAPS&EXCEPTIONS=text/xml&FORMAT=image/jpeg&SERVICE=WMTS&VERSION=1.0.0&REQUEST=GetTile&STYLE=normal&TILEMATRIXSET=PM&TILEMATRIX={z}&TILEROW={y}&TILECOL={x}',
    attribution:  '&copy; <a href="http://www.ign.fr">IGN-F Geoportail</a>'
    },
	{name: 'OSM - MapBox',
    layer: 'https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token=pk.eyJ1IjoicG5yLW5vcm1hbmRpZS1tYWluZSIsImEiOiJjam5idW02amEwMTJmM3FudmRjdDJqanJpIn0.UXJfuDjS8aLtDwXhyw5yOg',
    attribution:  '&copy; Contributeurs <a href="http://osm.org/copyright">OpenStreetMap</a> | &copy; MapBox'
    },
```


## 4 # Connexion FDW à GeoNature v1

Autoriser l'accès SSH entre les 2 VM.

Editer le fichier `/etc/init.d/iptables` et ajouter `/sbin/iptables -A INPUT -s 149.202.129.107 -j ACCEPT` dans `#Autorisation trafic locale`.

Puis executer la commade `sudo /etc/init.d/iptables` (via putty en root)

Créer un schéma `geonature_v1` dans `geonaturedb`
```sql
CREATE SCHEMA geonature_v1
  AUTHORIZATION geonatuser;
```

Commandes à executer en tant qu'utilisateur Postgres (donc depuis putty avec PSQL)

```sh
sudo -u postgres psql -d geonaturedb -c "CREATE extension IF NOT EXISTS postgres_fdw;"
sudo -u postgres psql -d geonaturedb -c "CREATE SERVER geonature_v1 FOREIGN DATA WRAPPER postgres_fdw OPTIONS (host '149.202.129.102', port '5432', dbname 'geonaturedb');"
sudo -u postgres psql -d geonaturedb -c "CREATE USER MAPPING FOR geonatuser SERVER geonature_v1 OPTIONS (user 'geonatuser', password '********');"
sudo -u postgres psql -d geonaturedb -c "GRANT ALL PRIVILEGES ON FOREIGN SERVER geonature_v1 TO geonatuser;"
```

Importer les `Foreign Table` nécessaires grâce à la nouvelle fonction (PG > 9.5) `IMPORT FOREIGN SCHEMA`
Exemple avec 
```sql
IMPORT FOREIGN SCHEMA utilisateurs
    FROM SERVER geonature_v1
    INTO geonature_v1;

IMPORT FOREIGN SCHEMA synthese
    FROM SERVER geonature_v1
    INTO geonature_v1;
```

## X # Récupérer les Taxons de la V1

Récupération des Taxons de la V1 pour peupler les listes de saisie en V2.
Adaptation du script proposé sur https://geonature.readthedocs.io/fr/latest/import-level-2.html

```sql
CREATE TABLE gn_imports.new_noms
(
  cd_nom integer NOT NULL,
  cd_ref integer NOT NULL,
  nom_fr character varying,
  array_listes integer[],
  CONSTRAINT new_noms_pkey PRIMARY KEY (cd_nom)
);

TRUNCATE TABLE gn_imports.new_noms;
INSERT INTO gn_imports.new_noms
SELECT DISTINCT
  i.cd_nom,
  t.cd_ref,
  split_part(t.nom_vern, ',', 1),
  array_agg(DISTINCT l.id_liste) AS array_listes
FROM geonature_v1.syntheseff i
LEFT JOIN taxonomie.taxref t ON t.cd_nom = i.cd_nom
LEFT JOIN taxonomie.bib_listes l ON id_liste = 100
WHERE i.cd_nom NOT IN (SELECT cd_nom FROM taxonomie.bib_noms)
AND t.cd_ref IS NOT NULL
GROUP BY i.cd_nom, t.cd_ref, nom_vern;

SELECT setval('taxonomie.bib_noms_id_nom_seq', (SELECT max(id_nom) FROM taxonomie.bib_noms), true);
INSERT INTO taxonomie.bib_noms(cd_nom, cd_ref, nom_francais)
SELECT cd_nom, cd_ref, nom_fr FROM gn_imports.new_noms;

INSERT INTO taxonomie.cor_nom_liste (id_liste, id_nom)
SELECT unnest(array_listes) AS id_liste, n.id_nom
FROM gn_imports.new_noms tnn
JOIN taxonomie.bib_noms n ON n.cd_nom = tnn.cd_nom;

DROP TABLE gn_imports.new_noms;
```


## X # Récupérer les observateurs


### Les structures :
l'UUID et l'ID sont générés automatiquement

Création des structures :


```sql
INSERT INTO
utilisateurs.bib_organismes(nom_organisme,adresse_organisme,cp_organisme,ville_organisme,tel_organisme,fax_organisme,email_organisme)
VALUES
('PNR Normandie-Maine','Maison du Parc','61320','Carrouges','02 33 81 75 75','','info@parc-normandie-maine.fr'),
('Association Faune et Flore de l''Orne','Moulin du Pont','61420','Saint-Denis-sur-Sarthon','02 33 26 26 62','','affo@wanadoo.fr'),
('Groupe Ornithologique des Avaloirs','','','','','','contact.goa53@yahoo.fr'),
('Conseil Départemental de l''Orne','Hôtel du Département\n27 boulevard de Strasbourg\nCS 30528','61017','Alençon Cedex','02 33 81 60 00','',''),
('Conservatoire botanique national de Brest','52, allée du Bot','29200','Brest','02 98 41 88 95','','cbn.brest@cbnbrest.com'),
('PNR du Perche','Maison du Parc\nCourboyer','61340','Nocé','02 33 25 70 10','','secretariat@parc-naturel-perche.fr'),
('PNR des Boucles de la Seine-Normande','692 rue du petit pont','76940','Notre-Dame-de-Bliquetuit','02 35 37 23 16','','contact@pnr-seine-normandie.com'),
('PNR des Marais du Cotentin et du Bessin','3 Village des Ponts Douve','50500','Carentan','02 33 71 65 30','','info@parc-cotentin-bessin.fr');
```
(NB1 : `\n` pour les retours à la ligne)
(NB2 : les ID correspondent à ceux de la V1 donc ce sera plus simple pour la suite : ne pas changer l'ordre !)


Mise à jour des rôles existants :
```sql
UPDATE utilisateurs.t_roles
   SET	nom_role='PNRNM_Equipe',
	desc_role='Tous les agents en poste au PNRNM',
	date_update=now()
 WHERE nom_role='Grp_en_poste';

UPDATE utilisateurs.t_roles
   SET	prenom_role='PNRNM',
	remarques='PNRNM',
	date_update=now()
 WHERE prenom_role='test';
 
UPDATE utilisateurs.bib_unites
   SET nom_unite='Equipe PNRNM'
 WHERE nom_unite='Service scientifique';

-- Import des utilisateurs V1
INSERT INTO utilisateurs.t_roles(groupe,
				identifiant,
				nom_role,
				prenom_role,
				desc_role,
				pass,
				email,
				id_organisme,
				organisme,
				id_unite,
				remarques,
				pn,
				session_appli,
				date_insert,
				date_update)

SELECT	FALSE::boolean as groupe,
	identifiant,
	nom_role,
	prenom_role,
	desc_role,
	pass,
	email,
	id_organisme,
	organisme,
	1::integer as id_unite,
	remarques || ' (compte récupéré de GeoNature v1)' as remarques,
	TRUE::boolean as pn,
	session_appli,
	now()::timestamp without time zone as date_insert,
	now()::timestamp without time zone as date_update
  FROM geonature_v1.t_roles
  WHERE id_organisme = 2
  AND nom_role NOT IN ('Administrateurs PNR NM','Stagiaires','Administrateur', 'Agents-PNR-NM', 'Agent');
  
-- Ajout manuel, depuis Usershub, des agents au groupe "PNRNM_Equipe", puis du groupe "PNRNM_Equipe" à l'application "Occtax" (ToDo : le faire en SQL...)  avec droits "Rédacteur"

```

Suppression du menu "Admin" pour les membres de PNRNM_Equipe
```sql
INSERT INTO utilisateurs.cor_app_privileges (id_tag_action, id_tag_object, id_application, id_role)
VALUES (
12, -- read
20, -- nothing
4, -- admin (Application backoffice de GeoNature)
7 -- PNRNM_Equipe (ex groupe EN POSTE)
);
```

### Les cadres d'acquisition / Jeux de données

Suppression du jeu de données ATBI de la réserve intégrale du Lauvitel dans le Parc national des Ecrins
```sql
-- Suppression du jeu de données ATBI de la réserve intégrale du Lauvitel dans le Parc national des Ecrins

DELETE FROM gn_meta.cor_dataset_actor
 WHERE id_dataset = 2;

DELETE FROM gn_meta.cor_dataset_territory
 WHERE id_dataset = 2;

DELETE FROM gn_meta.cor_dataset_protocol
 WHERE id_dataset = 2;
 

DELETE FROM gn_meta.t_datasets
 WHERE dataset_name = 'ATBI de la réserve intégrale du Lauvitel dans le Parc national des Ecrins';
```






Création via le module d'administration de GeoNature (ToDo : à faire en SQL ?)

Adaptation de la requête proposée par @donovanmaillard :

```sql
-- Création d'un Cadre général (parent) "ABC"
INSERT INTO gn_meta.t_acquisition_frameworks (
acquisition_framework_name,
acquisition_framework_desc,
id_nomenclature_territorial_level,
territory_desc,
keywords,
id_nomenclature_financing_type,
target_description,
acquisition_framework_start_date,
is_parent
)
VALUES
('Atlas Biodiversite Communale',
'Données acquise dans le Cadre d''un ABC',
'359',
'Etudes à l''échelle communale de communes de France Métropolitaine',
'ABC, Atlas de la Biodiversité Communale',
'390',
'',
'01/01/2018',
'1')


-- Création d'un cadre enfant "ABC Andaine Passais"
INSERT INTO gn_meta.t_acquisition_frameworks (
acquisition_framework_name,
acquisition_framework_desc,
id_nomenclature_territorial_level,
territory_desc,
keywords,
id_nomenclature_financing_type,
target_description,
acquisition_framework_start_date,
is_parent,
acquisition_framework_parent_id
)
VALUES
('ABC Andaine Passais',
'Données acquises dans le cadre de l''ABC Andaine Passais',
'359',
'Etudes à l''échelle communale de communes de France Métropolitaine',
'ABC, Atlas de la Biodiversité Communale, Andaine, Passais, Communauté de Communes',
'390',
'Données acquises dans le cadre de l''ABC Andaine Passais',
'01/01/2018',
'0',
'4')

-- Création d'un cadre "Données personnelles des agents du Parc Normandie-Maine"
INSERT INTO gn_meta.t_acquisition_frameworks (
acquisition_framework_name,
acquisition_framework_desc,
id_nomenclature_territorial_level,
territory_desc,
keywords,
id_nomenclature_financing_type,
target_description,
acquisition_framework_start_date,
is_parent
)
VALUES
('Données personnelles des agents du Parc Normandie-Maine',
'Données personnelles des agents du Parc Normandie-Maine',
'359',
'Etudes à l''échelle communale de communes de France Métropolitaine',
'Données personnelles, agents',
'390',
'Données personnelles des agents du Parc Normandie-Maine',
'01/01/2018',
'0')


-- Création d'un jeu de données pour l'ABC Andaine Passais
INSERT INTO gn_meta.t_datasets (
id_acquisition_framework,
dataset_name,
dataset_shortname,
dataset_desc,
id_nomenclature_data_type,
keywords,
marine_domain,
terrestrial_domain,
id_nomenclature_dataset_objectif,
id_nomenclature_collecting_method,
id_nomenclature_data_origin,
id_nomenclature_source_status,
id_nomenclature_resource_type,
default_validity
)
VALUES
('5',
'ABC Andaine Passais - Objectif Nature',
'ABC Andaine Passais - Objectif Nature',
'Données acquises dans le cadre de l''ABC Andaine Passais',
'326',
'ABC, Atlas de la Biodiversité Communale, Andaine, Passais',
'FALSE',
'TRUE',
'425',
'403',
'78',
'75',
'324',
'TRUE')

-- Création d'un jeu de données "Données personnelles des agents"
INSERT INTO gn_meta.t_datasets (
id_acquisition_framework,
dataset_name,
dataset_shortname,
dataset_desc,
id_nomenclature_data_type,
keywords,
marine_domain,
terrestrial_domain,
id_nomenclature_dataset_objectif,
id_nomenclature_collecting_method,
id_nomenclature_data_origin,
id_nomenclature_source_status,
id_nomenclature_resource_type,
default_validity
)
VALUES
('6', -- cadre "Données personnelles des agents"
'Données personnelles des agents du Parc Normandie-Maine',
'Données personnelles des agents du Parc Normandie-Maine',
'Données acquises par les agents sur leur temps personnel',
'326',
'Données personnelles',
'FALSE',
'TRUE',
'425',
'403',
'78',
'75',
'324',
'TRUE')


-- Renseignement acteurs dans les métadonnées 
-- Accordé à tout le groupe PNRNM_Equipe (id_role='7')
INSERT INTO gn_meta.cor_dataset_actor (
id_dataset, -- 3 pour le jeu ABC Andaine Passais - Objectif Nature
id_role, -- 7 pour PNRNM_Equipe
id_organism, -- 2 pour Parc Normandie-Maine
id_nomenclature_actor_role -- 371 pour 'Producteur du jeu de données'
)
VALUES
('3','7','2','371')


INSERT INTO gn_meta.cor_dataset_actor (
id_dataset, -- 4 pour le jeu "Données personnelles des agents"
id_role, -- 7 pour PNRNM_Equipe
id_organism, -- 2 pour Parc Normandie-Maine
id_nomenclature_actor_role -- 371 pour 'Producteur du jeu de données'
)
VALUES
('4','7','2','371')

```

