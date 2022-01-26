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


- Pour mettre en place l'authentification de l'Api, nous avons besoin de clé ssl, pour cela 

`mkdir config/jwt`

`openssl genrsa -out config/jwt/private.pem -aes256 4096` 
- En cas de souci avec la commande précédente vous pouvez utiliser `winpty` devant les commandes openssl

`openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem`


- Ensuite remplir la passphrase dans le fichier .env 


- Enfin lancé le serveur :

`php -S 127.0.0.1:8000 -t public/` 






