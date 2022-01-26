# Blog Symfony Formation Crédit Agricole

- Ce Projet est un blog exposé en Api et Application


- Pour démarrer ce projet, vous aurez besoin de récupérer les sources puis de vous placer dans un terminal à la racine du projet

Faire les commandes suivantes pour démarrer le projet :

`cp .env.example .env`

- Remplir les informations de connection à la base de données dans le fichier .env qui vient d'être créé

- Ensuite faire :

`composer install`

- Puis :

`php bin/console doctrine:migrations:migrate`

