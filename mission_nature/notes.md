# 1. Générer les fichiers JSON d'après les CSV

- mettre les CSV dans `/home/mission/Mission-Nature-scripts` via WinSCP

- en ligne de commande (putty), se placer dans ce dossier :
`cd /home/mission/Mission-Nature-scripts`

- executer la commande :
`php ./mission_tojson.php`

NB. : message erreur
`PHP Notice:  Undefined offset: 1 in /home/mission/Mission-Nature-scripts/mission                      _tojson.php on line 15`

(ignorer : lié à une ligne vide ??)


# 2. Redimensionner les photos pour les dossier thumb et full

- mettre les photos dans le dossier `Mission-Nature-scripts/photos` (via WinScp)

- lancer les commandes (putty) mogrify depuis ce dossier :
`cd /home/mission/Mission-Nature-scripts/photos`
`mogrify -path ../photos_thumb/ -thumbnail 256x256^ -gravity center -extent 256x256 *.jpg`

(manque celui des grandes photos)



# 3. Compilation de l'appli avec CORDOVA

- télécharger en local (sur `C:\ ` ) l'application sur Github :
https://github.com/NaturalSolutions/Mission-Nature-mobile

- mettre les photos (poster + thumbs) dans les dossiers `www/images/mission_taxons` et dans `www/data/image_source` (via WinSCP)

- avec CMD, se placer dans le dossier où a été téléchargée l'app :
`cd C:\mission_nature\Mission-Nature-mobile-master`

- executer ces commandes :

`npm install`
`cordova platform add android`

(très long)

Adapter `www\modules\main\config.js.tpl` en `config.js` avec :

coreUrl: `http://149.202.129.99/mission_nature`,

apiUrl: `http://149.202.129.99/mission_nature/obfmobileapi`,

https://github.com/PnEcrins/GeoNature-atlas/tree/master/static/images

cordova build android

Failed to find 'ANDROID_HOME' environment variable. Try setting it manually.
https://stackoverflow.com/questions/36198165/failed-to-find-android-home-environment-variable
https://cordova.apache.org/docs/en/latest/guide/platforms/android/index.html#requirements-and-support

> peut-être résolu ? (à vérifier)
