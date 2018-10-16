# Importation dans GeoNature v1 de la base SERENA mutualisée entre le PNR Normandie-Maine, le PNR Perche, l'AFFO et le Département de l'Orne

Ce document de travail permet de visualiser les étapes d'une procédure d'importation dans GeoNature des données d'une base SERENA (Access).
Ces étapes sont certainement optimisables (écrites il y a plusieurs années, avec un niveau balbutiant en SQL à l'époque).
Si vous identifiez des optimisations, n'hésitez pas à les proposer (en PR ou en me contactant)

## Importation des bases Access de SERENA dans une base PostGreSQL

L'importation a été réalisée avec l'outil "Access to PostgreSQL" (http://www.bullzip.com/products/a2p/info.php) avant que SERENA ne gère nativement le format PostGreSQL (non testé)
L'ensemble des tables des bases Access ont ainsi pu être récupérées dans une BDD PostGreSQL (tout dans le schéma "public", car l'outil ne gère pas (?) le schéma de destination)


## Mise à plat des données de SERENA


### Création d'une Vue Matérialisée (VM) des Sites

Création d'une table avec champ `geom` des sites, basée sur le champ `"SITE_CAR"`.
(NB : les guillemets "" sont importants car les noms des tables sont stockés en majuscules)

```sql
-- Création d'une VM (vue Matérialisée) des "Sites" utilisés dans la base SERENA (table RNF_SITE)

CREATE MATERIALIZED VIEW serena_affo_pnr.RNF_SITES_GEOM AS 
SELECT	"SITE_ID",
	"SITE_NOM",
	"RNF_CHOI"."CHOI_NOM",
	"SITE_CAR",
	split_part("SITE_CAR", ' ', 1) || ' - ' || split_part("SITE_CAR", ' ', 4) AS DATUM,
	CASE 
		WHEN split_part("SITE_CAR", ' ', 1)='L93F' THEN 2154
		WHEN split_part("SITE_CAR", ' ', 1)='LIIEF' THEN 27582
		ELSE 0
	END AS SRID,
	CAST(split_part("SITE_CAR", ' ', 2) as decimal)*1000 AS X,
	CAST(split_part("SITE_CAR", ' ', 3) as decimal)*1000 AS Y,
	ST_Transform(ST_GeomFromText('POINT(' || CAST(split_part("SITE_CAR", ' ', 2) as decimal)*1000 || ' ' || CAST(split_part("SITE_CAR", ' ', 3) as decimal)*1000 || ')', CASE 
		WHEN split_part("SITE_CAR", ' ', 1)='L93F' THEN 2154
		WHEN split_part("SITE_CAR", ' ', 1)='LIIEF' THEN 27582
		ELSE 0
	END),2154) AS GEOM
  FROM "RNF_SITE", "RNF_CHOI"
  WHERE "RNF_SITE"."SITE_CATEG_CHOI_ID" = "RNF_CHOI"."CHOI_ID"
  ORDER BY "SITE_ID";
```

### Création d'une Vue Matérialisée (VM) des Sites

A partir de la VM des Sites, création d'une table `RNF_OBSE_GEOM` des observations géolocalisées (au site si pas de XY).
Tous les champs de SERENA n'ont pas été conservés : seuls les champs principaux ont été gérés ici : une optimisation serait de récupérer tous les autres champs et de les mettre au format GeoNature.

```sql
-- création de la VM (vue matérialisée) "RNF_OBSE_GEOM", vue à plat de la BDD Serena, pour import dans GeoNature

-- execution : 10.9 secs (le 15/11/2017)
DROP	MATERIALIZED VIEW IF EXISTS RNF_OBSE_GEOM;
CREATE	MATERIALIZED VIEW RNF_OBSE_GEOM AS 

SELECT "OBSE_ID" , "OBSE_NOM", "OBSE_RELV_ID", "OBSE_OBSV_ID", "SRCE_COMPNOM_C", "OBSE_DETM_ID", "RELV_NOM", "RELV_PROP_LIBEL",
       "OBSE_TAXO_ID", "OBSE_DATE", CAST(left("OBSE_DATE",4) as int) AS "OBSE_ANNEE", "OBSE_TIME", "OBSE_DUR", "OBSE_SITE_ID", 
       "OBSE_HABI_ID", "OBSE_METHLOC_CHOI_ID", "OBSE_PCOLE_CHOI_ID", 
       "OBSE_VALIDAT_CHOI_ID", "OBSE_CONFID_CHOI_ID", "OBSE_PLACE", 
       "OBSE_LAT", "OBSE_LON", "OBSE_DUM", "OBSE_CAR", "OBSE_ALT", "OBSE_SIG_OBJ_ID", 
       "OBSE_WAYPOINT", "OBSE_SEX_CHOI_ID", "OBSE_STADE_CHOI_ID", "OBSE_AGE", 
       "OBSE_AGEUNIT_CHOI_ID", "OBSE_ABOND_CHOI_ID", "OBSE_NOMBRE", 
       "OBSE_PRECIS_CHOI_ID", "OBSE_SOCI_CHOI_ID", "OBSE_COMP_CHOI_ID", 
       "OBSE_COMPDIR_CHOI_ID", "OBSE_CONTACT_CHOI_ID", "OBSE_CONTACT2_CHOI_ID", 
       "OBSE_ACTIV_CHOI_ID", "OBSE_CARACT_CHOI_ID", "OBSE_ETAT_CHOI_ID", 
       "OBSE_DERANGO_CHOI_ID", "OBSE_DERANGI_CHOI_ID", "OBSE_TAXOREF", 
       "OBSE_NEBUL_CHOI_ID", "OBSE_PRECIP_CHOI_ID", "OBSE_VENT_CHOI_ID", 
       "OBSE_VENTDIR_CHOI_ID", "OBSE_VISIB_CHOI_ID", "OBSE_SOL_CHOI_ID", 
       "OBSE_TPTAIR", "OBSE_EAU_CHOI_ID", "OBSE_EAUDIR_CHOI_ID", "OBSE_MULTICR", 
       "OBSE_BAGUE", "OBSE_TEMP1", "OBSE_TEMP2", "OBSE_TEMP3", "OBSE_TEMP4", 
       "OBSE_TEMP5", "OBSE_TEMP6", "OBSE_COMMENT", "OBSE_CREA_DATH", 
       "OBSE_CREA_USER_ID", "OBSE_LMOD_DATH", "OBSE_LMOD_USER_ID", "OBSE_REPL_DATH", 
       "OBSE_REPL_ORIG",
	-- infos de validation issues de "RNF_CHOI"
       choixvalid."CHOI_NOM" as validite,
	-- données de la table Taxons ("RNF_TAXO")
	"RNF_TAXO"."TAXO_LATIN_C",
	CASE WHEN "RNF_TAXO"."TAXO_SYNONYM_ID" IS NULL THEN "RNF_TAXO"."TAXO_LATIN_C" ELSE taxovalid."TAXO_LATIN_C" END as "TAXO_REFESP",
	"RNF_TAXO"."TAXO_MNHN_ID",
	CASE WHEN "RNF_TAXO"."TAXO_SYNONYM_ID" IS NULL THEN "RNF_TAXO"."TAXO_MNHN_ID" ELSE taxovalid."TAXO_MNHN_ID" END as "TAXO_REFESP_ID",
	CASE WHEN "RNF_TAXO"."TAXO_SYNONYM_ID" IS NULL THEN "RNF_TAXO"."TAXO_FAM_C" ELSE taxovalid."TAXO_FAM_C" END as "TAXO_FAM_C",
	CASE WHEN "RNF_TAXO"."TAXO_SYNONYM_ID" IS NULL THEN "RNF_TAXO"."TAXO_ORD_C" ELSE taxovalid."TAXO_ORD_C" END as "TAXO_ORD_C",
	choixcateg."CHOI_NOM" as categorie,
	choixvalidtaxon."CHOI_NOM" as taxon_validit,
	CASE WHEN "RNF_TAXO"."TAXO_SYNONYM_ID" IS NULL THEN "RNF_TAXO"."TAXO_MNHN_ID" ELSE taxovalid."TAXO_MNHN_ID" END as "TAXO_MNHN_ID_VALID",
	"RNF_TAXO"."TAXO_SYNONYM_ID",
	"RNF_TAXO"."TAXO_VERNACUL",
       -- données de la VM sites (rnf_sites_geom)
       rnf_sites_geom."SITE_NOM",
       
       -- gestion des coordonnées des Observations
       -- règle : 	- si le champ OBSE_CAR (coordonnées XY avec Datum) est nul
       --			Cas 1 > si OBSE_LAT est nul, récupérer les coordonnées du SITE (SITE_CAR) et le type de site
       --			Cas 2 > récupérer les coordonnées OBSE_LAT / OBSE_LONG (WGS84 dans des champs différents)
       --		- si le champs OBSE_CAR est pas nul, convertir en LAMBERT 93 les coordonnées selon la source
       
       
       CASE WHEN "OBSE_CAR" IS NULL AND "OBSE_LAT" IS NULL THEN rnf_sites_geom."CHOI_NOM" ELSE 'XY Précis' END AS TYPE_GEOLOC,
       
       CASE WHEN "OBSE_CAR" IS NOT NULL THEN split_part("OBSE_CAR", ' ', 1) || ' - ' || split_part("OBSE_CAR", ' ', 4) WHEN Cast(SUBSTR("OBSE_LAT",3,1000) as numeric) > 1 THEN 'WGS84' ELSE 'SITE' END AS DATUM,
       
       CASE
		WHEN "OBSE_CAR" IS NULL AND "OBSE_LAT" IS NULL 
			THEN rnf_sites_geom.geom
		WHEN "OBSE_CAR" IS NULL AND "OBSE_LAT" IS NOT NULL AND LEFT("OBSE_LON",2) <> 'XD' AND Cast(SUBSTR("OBSE_LAT",3,1000) as numeric) > 1 -- Quand pas XD dans OBSE_LON, la valeur est positive (° Est)
			THEN ST_Transform(ST_GeomFromText('POINT(' || Cast(SUBSTR("OBSE_LON",3,1000) as numeric) || ' ' || Cast(SUBSTR("OBSE_LAT",3,1000) as numeric) ||')', 4326),2154)
		WHEN "OBSE_CAR" IS NULL AND "OBSE_LAT" IS NOT NULL AND LEFT("OBSE_LON",2) = 'XD' AND Cast(SUBSTR("OBSE_LAT",3,1000) as numeric) > 1  -- Quand XD dans OBSE_LON, la valeur est négative (° Ouest)
			THEN ST_Transform(ST_GeomFromText('POINT(' || -1*Cast(SUBSTR("OBSE_LON",3,1000) as numeric) || ' ' || Cast(SUBSTR("OBSE_LAT",3,1000) as numeric) ||')', 4326),2154)
		ELSE
			ST_Transform(ST_GeomFromText('POINT(' || CAST(split_part("OBSE_CAR", ' ', 2) as decimal)*1000 || ' ' || CAST(split_part("OBSE_CAR", ' ', 3) as decimal)*1000 || ')', CASE 
				WHEN split_part("OBSE_CAR", ' ', 1)='L93F' THEN 2154
				WHEN split_part("OBSE_CAR", ' ', 1)='LIIEF' THEN 27582
				ELSE 2154
				END),2154)
		END
	AS GEOM
  FROM (("RNF_OBSE" JOIN rnf_sites_geom ON "RNF_OBSE"."OBSE_SITE_ID" = rnf_sites_geom."SITE_ID") JOIN "RNF_SRCE" ON "RNF_OBSE"."OBSE_OBSV_ID" = "RNF_SRCE"."SRCE_ID")
  JOIN	"RNF_RELV" ON "RNF_OBSE"."OBSE_RELV_ID" = "RNF_RELV"."RELV_ID"
  JOIN	"RNF_CHOI" as choixvalid ON "RNF_OBSE"."OBSE_VALIDAT_CHOI_ID" = choixvalid."CHOI_ID"
  JOIN	"RNF_TAXO" ON "RNF_OBSE"."OBSE_TAXO_ID" = "RNF_TAXO"."TAXO_ID"
  JOIN	"RNF_CHOI" as choixcateg ON "RNF_TAXO"."TAXO_CATEG_CHOI_ID" = choixcateg."CHOI_ID"
  JOIN	"RNF_CHOI" as choixvalidtaxon ON "RNF_TAXO"."TAXO_VALI_CHOI_ID" =choixvalidtaxon."CHOI_ID"
  LEFT JOIN	"RNF_TAXO" as taxovalid ON "RNF_TAXO"."TAXO_SYNONYM_ID" = taxovalid."TAXO_ID" ;


-- ajout de l'index spatial (5.9 s)
CREATE INDEX sidx_rnf_obse_geom_geom ON rnf_obse_geom USING gist (geom);

```
