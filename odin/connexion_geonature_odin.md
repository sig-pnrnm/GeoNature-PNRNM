# Connexion entre une base GeoNature (V2) et ODIN

Pas à pas pour la connexion de l'instance GeoNature du Parc Normandie-Maine à ODIN (https://biodiversite.normandie.fr/SINP/odin)

La connexion entre les 2 bases de données PostGreSQL va utiliser les Foreign Data Wrapper (FDW) de PostGreSQL.

## Création, dans la base de données GeoNature, d'un utilisateur ODIN

(commande psql à executer depuis putty avec l'utilisateur root)

```shell
su - postgres
```
on est alors en postgres@debian

```sql
psql -c "CREATE USER odin WITH PASSWORD '***motdepasse***' "
```

## Création d'un schéma "partenaires" dans la base geonaturedb

depuis PgAdmin (connecté en tant que "geonatuser")

```sql
CREATE SCHEMA partenaires ;
GRANT USAGE ON SCHEMA partenaires TO odin;
GRANT SELECT ON ALL TABLES IN SCHEMA partenaires TO odin; -- ne marche pas pour les vues ???
GRANT SELECT ON partenaires.synthese_pnrnm_odin TO odin;
```



## Foreign Data Wrapper depuis le serveur ODIN

```sql
CREATE EXTENSION IF NOT EXISTS postgres_fdw;

-- remplacer 'XXX.XXX.XXX.XXX' par l'IP du serveur GeoNature v2
CREATE SERVER geonature_pnrnm
        FOREIGN DATA WRAPPER postgres_fdw
        OPTIONS (host 'XXX.XXX.XXX.XXX', port '5432', dbname 'geonaturedb');

CREATE USER MAPPING FOR local_user
        SERVER geonature_pnrnm
        OPTIONS (user 'odin', password '***motdepasse***');

CREATE FOREIGN TABLE synthese_pnrnm_orbpdl (--définition de la table à faire ! (ToDo !)
											)
        SERVER geonature_pnrnm
        OPTIONS (schema_name 'partenaires', table_name 'synthese_pnrnm_orbpdl');
```
