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

### Initialisation

Après le démarrage, il faut exécuter ces commandes pour le système de notification par mails :

1. **Configurer RabbitMQ (Queues & Exchanges)** :

    ```bash
    docker compose exec api.rdv php src/console/setup_rabbitmq.php
    ```

2. **Démarrer le consommateur d'emails** :

    ```bash
    docker compose exec -d mailer php src/console/consume.php
    ```


## Structure du Projet

L'application est découpée en micro-services indépendants :

- **`gateway/`** : Point d'entrée unique (Port 80/6081). Redirige les requêtes.
- **`app-auth/`** : Service d'Authentification (JWT).
- **`app-rdv/`** : Gestion des Rendez-vous (Business Logic).
- **`app-praticiens/`** : Annuaire des praticiens.
- **`app-mailer/`** : Service autonome d'envoi d'emails (via RabbitMQ).
- **`sql/`** : Scripts d'initialisation des bases de données PostgreSQL.
- **`tests/bruno/`** : Collection de tests API prête à l'emploi.

## Guide de Test avec Bruno

Bruno est un client API (comme Postman) open-source. Une collection complète est incluse dans le projet.

### 1. Importer la Collection

1. Ouvrez **Bruno**.
2. Cliquez sur **"Open Collection"**.
3. Sélectionnez le dossier : `toubilib-microservices---API-Gateway/tests/bruno`.
4. La collection "Toubilib API" apparaît avec les dossiers `Auth`, `Praticiens`, `RendezVous`.

### 2. Configurer l'Environnement

1. En haut à droite, cliquez sur "No Environment" -> **Configure**.
2. Vérifiez ou créez un environnement `dev` avec la variable :
    - `baseUrl`: `http://localhost:6081`
3. Sélectionnez cet environnement `dev`.


### 3. Exécuter les Tests

L'ordre logique est le suivant :

1. **S'authentifier** :
    - Allez dans `Auth` -> `Signin`.
    - Exécutez la requête ("Send").
    - *Note : Le token JWT est automatiquement sauvegardé par Bruno pour les requêtes suivantes.*

2. **Parcourir les Praticiens** :
    - Allez dans `Praticiens` -> `List All`.
    - Récupérez un ID de praticien.

3. **Prendre Rendez-vous** :
    - Allez dans `RendezVous` -> `Create RDV`.
    - Modifiez le JSON (body) si besoin (Date, Praticien ID).
    - Cliquez sur "Send".

4. **Vérifier la Notification** :
    - Ouvrez **[http://localhost:1080](http://localhost:1080)** (MailCatcher).
    - Un email de confirmation doit être apparu.

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
