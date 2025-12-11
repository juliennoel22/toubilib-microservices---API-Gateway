# Toubilib

API de gestion de rendez-vous médicaux (Prise de RDV, gestion praticiens/patients). Ce projet répond aux besoins de gestion de rendez-vous médicaux via une architecture micro-services simulée avec Docker.

## Prérequis

- [Docker](https://www.docker.com/)
- [Docker Compose](https://docs.docker.com/compose/)

## Installation et Configuration

### Fichiers `.env`

Le projet utilise plusieurs fichiers `.env` situés à la racine pour configurer les bases de données (PostgreSQL). Pour obtenir ces fichiers, il faut les copier depuis les fichiers `.env.dist` :

- `toubipratdb.env` : Base praticiens
- `toubiauthdb.env` : Base authentification
- `toubirdvdb.env` : Base rendez-vous
- `toubipatientdb.env` : Base patients

Il faut également copier les fichiers `.env.dist` dans `app/config/`.

### Démarrage

Pour construire et lancer l'application :

```bash
docker-compose build
docker-compose up -d
```

### Arrêt

Pour arrêter les conteneurs :

```bash
docker-compose down
```

## Fonctionnalités Réalisées

L'API exposes les points de terminaisons suivants. Certaines routes nécessitent une authentification (JWT).

### Authentification
- `POST /auth/signin` : Connexion (obtention du token JWT).
- `POST /auth/refresh` : Rafraîchissement du token.

### Praticiens
- `GET /praticiens` : Lister tous les praticiens.
- `GET /praticiens/{id}` : Obtenir les détails d'un praticien.
- `GET /praticiens/villes/{ville}` : Rechercher des praticiens par ville.
- `GET /praticiens/specialites/{specialite}` : Rechercher des praticiens par spécialité.
- `GET /praticiens/{id}/agenda` : Consulter l'agenda d'un praticien **(Authentification requise)**.
- `GET /praticiens/{id}/rdvs` : Lister les rendez-vous d'un praticien.
- `GET /praticiens/{id}/creneaux` : Lister les créneaux occupés.

### Rendez-vous
- `POST /rdvs` : Créer un rendez-vous **(Authentification requise + Validation)**.
- `GET /rdvs/{id}` : Consulter un rendez-vous **(Authentification requise)**.
- `PATCH /rdvs/{id}/annuler` : Annuler un rendez-vous **(Authentification requise)**.
- `PATCH /rdvs/{id}/honorer` : Marquer un rendez-vous comme honoré **(Authentification requise)**.
- `PATCH /rdvs/{id}/ne-pas-honorer` : Marquer un rendez-vous comme non honoré **(Authentification requise)**.

## Accès

- **API** : Accessible via [http://localhost:6080](http://localhost:6080).
- **Adminer** (Gestion BDD) : Accessible via [http://localhost:8080](http://localhost:8080).
