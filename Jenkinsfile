pipeline {
    agent any
    stages {
        stage('Checkout') {
            steps {
               git branch: 'main', url: 'https://github.com/GwendalBiotteau/EPSI-MSPR4-API.git'
            }
        }
        stage('Fichier d’environnement') {
            steps {
                // Renommer le fichier .env.local.template en .env.local
                bat 'type .env.local'
            }
        }
        stage('Tests unitaires + Générer le rapport de test') {
            steps {
                // Exécuter les tests unitaires avec PHPUnit
                bat 'php vendor/bin/phpunit'
		            // Collecter les résultats des tests unitaires avec PHPUnit
        	      junit '**/junit.xml'
		            // Générer le rapport de test avec Test Result Analyzer
        	      step([$class: 'JUnitResultArchiver', testResults: '**/junit.xml'])
            }
        }
	    stage('Création d'un tag git') {
            steps {
                script {
                    bat 'echo "creating tag only"'
                    bat '.\tag_version.bat'
                }
            }
        }
        stage('Build Image Docker') {
            steps {
                // Build l'image docker avec docker-compose
                bat 'docker-compose --env-file ./.env.local build'
            }
        }

        stage('Démarrer Docker') {
            steps {
                // Démarrer les conteneurs docker avec docker-compose
                bat 'docker-compose --env-file ./.env.local up -d'
            }
        }

        stage('Ouvrir le terminal docker') {
            steps {
                // Ouvrir le terminal docker avec docker-compose
                bat 'docker-compose --env-file ./.env.local exec php zsh'
            }
        }

        stage('Installer Application') {
            steps {
                // Installer l'application avec composer et symfony console
                bat 'composer install --ignore-platform-reqs'
                bat 'php bin/console doctrine:database:create --if-not-exists'
                bat 'php bin/console doctrine:schema:drop --full-database --force'
                bat 'php bin/console doctrine:schema:update --force'
                bat 'php bin/console doctrine:fixtures:load --append'
            }
        }
         stage('Pousser dans DockerHub') {
            steps {
                bat 'docker login -u hubdockerpizza -p dckr_pat_ScavJMPClCjj7j5V0K6UCfgLfYU'
                bat 'docker tag revendeur_api boudjemaa/revendeur_api:latest'
                bat 'docker push boudjemaa/revendeur_api:latest'
            }
        }
    }
}
