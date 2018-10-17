# Connexion entre une base GeoNature (V2) et ODIN

Pas à pas pour la connexion de l'instance GeoNature du Parc Normandie-Maine à ODIN (https://biodiversite.normandie.fr/SINP/odin)

La connexion entre les 2 bases de données PostGreSQL va utiliser les Foreign Data Wrapper (FDW) de PostGreSQL.

## Création, dans la base de données GeoNature, d'un utilisateur ODIN

(commande psql à executer depuis putty avec l'utilisateur geonatureadmin)

```shell
sudo su postgres
```
on est alors en postgres@geonature2

```sql
psql -c "CREATE USER odin WITH PASSWORD '***motdepasse***' "
```

## Création d'un schéma "partenaires" dans la base geonaturedb

depuis PgAdmin (connecté en tant que "geonatuser")

```sql
CREATE SCHEMA partenaires ;
GRANT USAGE ON SCHEMA partenaires TO odin;

-- création d'une vue matérialisée (VM) 'synthese_pnrnm_odin' depuis la synthèse de GeoNature (à faire !!!)

GRANT SELECT ON partenaires.synthese_pnrnm_odin TO odin;
```


## Création des vues OBSERVATIONS et SITUATIONS attendues par ODIN

Création des 2 VM : `vm_observation_view` et `vm_situation_view` aux formats attendus.


```sql
-- Creation d'une VM 'vm_observation_view' pour ODIN
DROP	MATERIALIZED VIEW IF EXISTS partenaires.vm_observation_view ;

CREATE	MATERIALIZED VIEW partenaires.vm_observation_view AS
 SELECT	s.unique_id_sinp	as	o_uuid,		-- UUID généré par GeoNature v2
	s.id_synthese::text	as	o_id,		-- ID dans la Synthèse de GeoNature v2
	t.cd_nom::text		as	o_reid,		-- identifiant TaxRef
	s.meta_v_taxref::text	as	o_refe,		-- version de TaxRef (Taxref V11.0 actuellement)
	t.nom_valide::text	as	o_nlat,		-- nom latin (nom valide)
	s.count_max::real	as	o_nbre,		-- nombre max
	'nombre d''individus'::text as	o_nbrt,		-- type de dénombrement (à affiner ?)
	communes.insee_com::text as	o_admi,		-- insee commune
	null::text		as	o_situ,		-- identifiant du site (non concerné ici : coordonnées précises)
	to_char(s.date_min, 'DD/MM/YYYY')::text as o_date,	-- date min
	CASE	WHEN s.date_max <> s.date_min
		THEN to_char(s.date_max, 'DD/MM/YYYY')::text
		ELSE null::text
	END 			as	o_date2,	-- date max (pour gérer les périodes)
	s.observers		as	o_obsv,		-- Observateurs
	'PNR Normandie-Maine'::text as	o_strp,		-- Structure productrice (à faire : filtrer par source quand la base sera mutualisée)	
	'D'::text		as	o_styp,		-- donnée terrain (à affiner ensuite)
	'observation aléatoire' as	o_sacq,		-- gros travail de mise en correspondance à faire !!!
	d.dataset_name::text	as	o_spmc		-- jeu de données
	-- autres champs à finaliser
   FROM gn_synthese.synthese s
     JOIN taxonomie.taxref t ON t.cd_nom = s.cd_nom
     JOIN gn_meta.t_datasets d ON d.id_dataset = s.id_dataset
     JOIN gn_synthese.t_sources sources ON sources.id_source = s.id_source
     JOIN gn_synthese.v_synthese_decode_nomenclatures deco ON deco.id_synthese = s.id_synthese
     JOIN ref_geo.l_areas areas ON ST_INTERSECTS(areas.geom, s.the_geom_local)
     JOIN ref_geo.li_municipalities communes ON areas.id_area = communes.id_area

GRANT SELECT ON partenaires.vm_observation_view TO odin;


-- Création d'une VM 'vm_situation_view' des "situations géographiques" pour ODIN:

DROP	MATERIALIZED VIEW IF EXISTS partenaires.vm_situation_view ;

CREATE	MATERIALIZED VIEW partenaires.vm_situation_view AS 
 SELECT	-- champs à selectionner !
	-- ...
   FROM gn_synthese.synthese s
     JOIN taxonomie.taxref t ON t.cd_nom = s.cd_nom
     JOIN gn_meta.t_datasets d ON d.id_dataset = s.id_dataset
     JOIN gn_synthese.t_sources sources ON sources.id_source = s.id_source
     JOIN gn_synthese.v_synthese_decode_nomenclatures deco ON deco.id_synthese = s.id_synthese
     LIMIT 100;
GRANT SELECT ON partenaires.vm_situation_view TO odin;
```


## Foreign Data Wrapper depuis le serveur ODIN

(pour info, mais à gérer côté ODIN)

```sql
CREATE EXTENSION IF NOT EXISTS postgres_fdw;

-- remplacer 'XXX.XXX.XXX.XXX' par l'IP du serveur GeoNature v2
CREATE SERVER geonature_pnrnm
        FOREIGN DATA WRAPPER postgres_fdw
        OPTIONS (host 'XXX.XXX.XXX.XXX', port '5432', dbname 'geonaturedb');

CREATE USER MAPPING FOR local_user -- à remplacer par le user sur le serveur Odin
        SERVER geonature_pnrnm
        OPTIONS (user 'odin', password '***motdepasse***');

CREATE FOREIGN TABLE synthese_pnrnm_odin (--définition de la table à faire ! (ToDo !)
											)
        SERVER geonature_pnrnm
        OPTIONS (schema_name 'partenaires', table_name 'synthese_pnrnm_odin');
```
