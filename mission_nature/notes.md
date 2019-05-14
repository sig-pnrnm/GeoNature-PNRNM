# Pense bête pour la compilation de l'application Mission Nature


https://github.com/NaturalSolutions/Mission-Nature-mobile

https://github.com/NaturalSolutions/Mission-Nature-scripts

en complément notamment de https://github.com/NaturalSolutions/Mission-Nature-scripts/blob/master/mission_manual.rst



## 1. Générer les fichiers JSON d'après les CSV

- mettre les CSV `missions.csv` et `taxons.csv` dans `/home/mission/Mission-Nature-scripts` via WinSCP

(question : et pour les CSV `cities.csv` et `credits_photos.csv` ?)

réponses :
credits_photos.csv > avec site csv to json
et placer le json dans le dossier data

cities.csv > ne pas y toucher pour l'instant car il y a un index


- en ligne de commande (putty), se placer dans ce dossier :

`cd /home/mission/Mission-Nature-scripts`

- executer la commande :

`php ./mission_tojson.php`

NB. : messages erreurs : `PHP Notice:  Undefined offset: 1 in /home/mission/Mission-Nature-scripts/mission_tojson.php on line 15`
(ignorer : lié à une ligne vide ??)


## 2. Redimensionner les photos pour les dossier thumb et full

- mettre les photos dans les dossiers `photos` du répertoire `Mission-Nature-scripts/mission_taxon/` (via WinScp)

- lancer les commandes (putty) mogrify depuis les dossiers `photos` (pour taxons et missions) :

`cd /home/mission/Mission-Nature-scripts/mission_taxon/taxon/photos`
`mogrify -path ../thumbs -thumbnail 256x256^ -gravity center -extent 256x256 *.jpg`
`mogrify -path ../poster/ -resize 900x900 *.jpg`

* /!\ nb : incohérence avec les répertoires de l'appli : /thumbs et /poster et non /photos_thumb et /photos_full*


## 3. Compilation de l'appli avec CORDOVA

- télécharger en local (sur `C:\mission_nature` ) l'application sur Github :
https://github.com/NaturalSolutions/Mission-Nature-mobile

- mettre les photos en local depuis le serveur Mission Nature (poster + thumbs) dans les dossiers `www/images/mission_taxons` et dans `www/data/image_source` (via WinSCP)

- (question : ne devrait-on pas mettre aussi le fichier `missions.json`)

- Adapter `www\modules\main\config.js.tpl` en `config.js` avec :
```
  coreUrl: 'http://149.202.129.99/mission_nature',
  apiUrl: 'http://149.202.129.99/mission_nature/obfmobileapi',
```
https://github.com/PnEcrins/GeoNature-atlas/tree/master/static/images

- avec CMD, se placer dans le dossier où a été téléchargée l'app :

`cd C:\mission_nature\Mission-Nature-mobile-master`

- executer ces commandes :

`npm install` (execution : 13s)

`cordova platform add android`

(normalement très long. Mais si plateform déjà ajoutée, rapide ('Android plateform already added'))


`cordova build android`

> erreur (mars)
```
Failed to find 'ANDROID_HOME' environment variable. Try setting it manually.
https://stackoverflow.com/questions/36198165/failed-to-find-android-home-environment-variable
https://cordova.apache.org/docs/en/latest/guide/platforms/android/index.html#requirements-and-support
```
> peut-être résolu ? (à vérifier)

> erreur (mai)
(en cours d'execution)
```
Installing "cordova-plugin-compat" for android
Plugin doesn't support this project's cordova-android version. cordova-android:
6.4.0, failed version requirement:
      <6.3.0
Skipping 'cordova-plugin-compat' for android
```

(au final)
```
BUILD SUCCESSFUL in 9s
1 actionable task: 1 executed
Subproject Path: CordovaLib
Starting a Gradle Daemon, 1 incompatible and 1 stopped Daemons could not be reus
ed, use --status for details
The Task.leftShift(Closure) method has been deprecated and is scheduled to be re
moved in Gradle 5.0. Please use Task.doLast(Action) instead.
        at build_64w8y75zi1vhffm6ihvwftlrx.run(C:\mission_nature\Mission-Nature-
mobile-master\platforms\android\build.gradle:141)
Configuration 'compile' in project ':' is deprecated. Use 'implementation' inste
ad.
File C:\Users\Sylvain\.android\repositories.cfg could not be loaded.
Checking the license for package Android SDK Platform 26 in C:\Users\Sylvain\App
Data\Local\Android\Sdk\licenses
Warning: License for package Android SDK Platform 26 not accepted.

FAILURE: Build failed with an exception.

* What went wrong:
A problem occurred configuring root project 'android'.
> You have not accepted the license agreements of the following SDK components:
  [Android SDK Platform 26].
  Before building your project, you need to accept the license agreements and co
mplete the installation of the missing components using the Android Studio SDK M
anager.
  Alternatively, to learn how to transfer the license agreements from one workst
ation to another, go to http://d.android.com/r/studio-ui/export-licenses.html

* Try:
Run with --stacktrace option to get the stack trace. Run with --info or --debug
option to get more log output.

* Get more help at https://help.gradle.org

BUILD FAILED in 15s
cmd: Command failed with exit code 1 Error output:
FAILURE: Build failed with an exception.

* What went wrong:
A problem occurred configuring root project 'android'.
> You have not accepted the license agreements of the following SDK components:
  [Android SDK Platform 26].
  Before building your project, you need to accept the license agreements and co
mplete the installation of the missing components using the Android Studio SDK M
anager.
  Alternatively, to learn how to transfer the license agreements from one workst
ation to another, go to http://d.android.com/r/studio-ui/export-licenses.html

* Try:
Run with --stacktrace option to get the stack trace. Run with --info or --debug
option to get more log output.

* Get more help at https://help.gradle.org

BUILD FAILED in 15s
```
