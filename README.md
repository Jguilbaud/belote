# Belote
* Auteur : Johan GUILBAUD
* Prérequis : PHP7.4+ & MySQL
* Statut : En cours de développement

## Configuration
Copier le fichier src/conf/conf.inc.php.dist en conf.inc.php et y mettre la configuration adéquate

### Installation dans un sous répertoire (alias web)
Si installé dans un sosu répertoire (par exmeple monsite.com/belote/) mettre à jour le fichier .htaccess

## Lancement serveur mercure (debug)
./mercure --jwt-key='myjwt' --addr=':3000' --debug --cors-allowed-origins='http://localhost'

