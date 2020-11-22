# Jeu
## Titre 
Conception d'un serveur REST sur Symfony
## Description
Le but de ce projet est de fournir un genre de terrain de jeu pour personnages via un serveur REST. 
Le serveur sera capable d’accepter plusieurs joueurs en même temps et de gérer les actions des joueurs. 
Le protocole repose sur des requêtes POST / GET.
## Actions utilisateur
- la connexion du joueur (sans authentification)
- l’observation d’une pièce (retourner son état)
- le déplacement d’un joueur vers une pièce adjacente
- l’observation d’un autre joueur/monstre (retourner l’état d’un joueur/monstre)
- mener une attaque vers un autre joueur si on est dans la même pièce
