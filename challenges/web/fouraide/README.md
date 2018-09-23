# Four Aide
## Description
Le réseau social le plus chaud de l'heure.

## Flag
DCI{dbc408fc96d7a3cde7cbe5296c015759}

## Difficulté
Pas évident. En tout cas je pense.

Ca demande certains skills de Node.js pour trouver/comprendre le code source, il y a de la manipulation de web socket à faire, de l'injection de MongoDB, de la pollution de prototype et un peu de Hashcat.

Concernant Hashcat, il y a un password de 6 lettres minuscules à cracker qui est hinté dans le code source de la page principale. J'ai testé sur mon (vieux mais quand même pas pire) laptop, ca prends en 20 secondes et ~10 minutes avec un script batard en Ruby, 15 secondes avec Hashcat. J'imagine c'est assez fesable pour la majorité du monde.

## Déploiement
J'ai fournis une configuration pour `docker-compose` pour automatiser toute les containers. Tout passe par un port HTTP (ici 1234) géré par un serveur NGINX. J'ai pris des image de Alpine pour que ca soit plus slim.

`docker-compose up` devrait être suffisant pour démarrer le challenge. Le données sont pas persistées. À chaque démarrage, la base de donnée est vide et l'utilisateur "admin" est recréé.

Concernant les ressources utilisés, chaque connexion au serveur de web socket spawn un nouveau process de node.js et une nouvelle connexion à MongoDB. Les connexions à MongoDB sont cap à genre 1000. À voir si c'est nécéssaire de changer cette config.
