# EPSI-MSPR4-API
*Groupe 1 : Benjamin FERRANDEZ - Gwendal BIOTTEAU - Joel KONGOLO BEYA - Myriam ZALANI - Roumaissa MOUNIR*

## API
La documentation de l'API sera mise à disposition sur se répertoire prochainement...

## Informations du projet
Ce projet est développé en [PHP](https://www.php.net/) avec le framework [Symfony](https://symfony.com/). Il intègre la configuration Docker crée par [Gwendal BIOTTEAU](https://github.com/GwendalBiotteau/) (membre du projet) suivante : https://github.com/GwendalBiotteau/docker-symfony.

## Prérequis
Afin d'installer localement le projet, les outils nécessaires doivent préalablement être installés sur votre machine :
- **Docker & Docker Compose** : https://docs.docker.com/compose/install/
- **Git** : https://github.com/git-guides/install-git
- (optionnel) **Make** : https://www.gnu.org/software/make/

## Installation et utilisation du projet
L'installation du projet comprends plusieurs étapes. Certaines étapes peuvent être réalisées de différente manière selon l'utilisation de **Make** ou non par exemple. Les différents choix seront matérialisés devant chaque commande dans la liste à puce des commandes à réaliser pour l'étape.

### Clone du projet :
- **HTTPS** : `git clone https://github.com/GwendalBiotteau/EPSI-MSPR4-API.git`
- **SSH** : `git clone git@github.com:GwendalBiotteau/EPSI-MSPR4-API.git`

### Accéder au projet :
- `cd EPSI-MSPR4-API`

### Configurer le fichier d’environnement
- Renommer le fichier `.env.local.template` en `.env.local` (celui ci ne devra pas être commit sur Git d'où sa présence dans le fichier `.gitignore`)
- Remplir le contenu des variables disponibles ou remplacer le fichier par un fichier pré-configuré qui vous a été transmit au préalable

### Build l'image docker :
- **CLI** : `docker-compose --env-file ./.env.local build`
- **MAKE** : `make docker_build`

### Démarrer docker :
- **CLI** : `docker-compose --env-file ./.env.local up -d`
- **MAKE** : `make start`

### Ouvrir le terminal docker :
- **CLI** : `docker-compose --env-file ./.env.local exec php zsh`
- **MAKE** : `make bash`

### Installer l'application :
- **CLI** :
  - `composer install --ignore-platform-reqs`
  - `php bin/console doctrine:database:create --if-not-exists`
  - `php bin/console doctrine:schema:drop --full-database --force`
  - `php bin/console doctrine:schema:update --force`
- **MAKE** :
  - `make install`

##  Mise à jour du projet suite à un `git pull`
Ces étapes peuvent être adaptées en fonction des modifications apportées depuis le dernier pull.

- Reprendre les consignes d'installation à partir de [Démarrer Docker](#démarrer-docker)

## Utilisation du projet
- L'**API** est accessible **en local** sur l'URL [http://localhost/](http://localhost/) ou [http://127.0.0.1/](http://127.0.0.1/)
- **phpMyAdmin** est accessible **en local** sur le port `81` : [http://localhost:81/](http://localhost:81/)
- **Adminer** est accessible **en local** sur le port `8182` : [http://localhost:8182/](http://localhost:8182/)

### Arrêter Docker après utilisation
#### Quitter le terminal docker :
- `exit`
#### Arrêter docker :
- **CLI** : `docker-compose --env-file ./.env.local stop` 
- **MAKE** : `make stop`

## Bonnes pratiques
Afin de collaborer correctement durant le développement de l'API, les règles suivantes devront être respectées :
- Chaque nouveau développement devra passer par la création d'une branche + Pull Request dédiées nommée suivant le format suivant :
  - **feature/{titre de la feature}** pour les fonctionnalités
  - **hotfix/{titre du hotfix}** pour les corrections
- Chaque commit devra contenir un message clair expliquant les changements effectués **en anglais**
- Les commentaires dans le code devront être rédigés **en anglais**
- Les normes [PSR](https://www.php-fig.org/psr/) devront être respectées dans la mesure du possible