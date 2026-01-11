# Suivi des Fonctionnalités du Projet

Ce fichier répertorie l'état d'avancement des fonctionnalités (Sujet 1 à 13).

## Fonctionnalités Principales

- [✅] **1. Lister les praticiens**
  - *Route :* `/praticiens`
  - *Action :* `ListerPraticiensAction`

- [✅] **2. Afficher le détail d’un praticien**
  - *État :* **[!] Bug**
  - *Route :* `/praticiens/{id}`
  - *Action :* `ListerPraticienIdAction`

- [✅] **3. Lister les créneaux de rdvs déjà occupés**
  - *État :* **[!] Bug**
  - *Détail :* La route `/praticiens/{id}/creneaux` existe mais le fichier est mal nommé (`ListerCreneauxOccActions.php` vs classe `Single`). **À corriger.**

- [✅] **4. Consulter un rendez-vous**
  - *Route :* `/rdvs/{id}`
  - *Action :* `ConsulterRendezVousAction`

- [✅] **5. Réserver un rendez-vous**
  - *Route :* `POST /rdvs`
  - *Action :* `CreerRendezVousAction`

- [✅] **6. Annuler un rendez-vous**
  - *Route :* `PATCH /rdvs/{id}/annuler`

- [✅] **7. Afficher l’agenda d’un praticien**
  - *Route :* `/praticiens/{id}/agenda` (Affiche aussi motif/patient)

- [✅] **8. S’authentifier (Patient/Praticien)**
  - *Route :* `/auth/signin`
  - *Support :* Authentification JWT opérationnelle.

## Fonctionnalités Additionnelles

- [✅] **9. Rechercher un praticien (Spécialité/Ville)**
  - *Routes :* `/praticiens/villes/{ville}` et `/praticiens/specialites/{specialite}`

- [✅] **10. Gérer le cycle de vie (Honoré/Non honoré)**
  - *Routes :* `PATCH /rdvs/{id}/honorer` et `.../ne-pas-honorer`

- [✅] **11. Historique des consultations d’un patient**
  - *État :* **À Vérifier**
  - *Détail :* Pas de route explicite pour l'historique *patient*. Voir si `/rdvs` filtré suffit ou s'il manque une implémentation.

- [✅] **12. S’inscrire en tant que patient**
  - *État :* **À faire**
  - *Détail :* Code "WIP" dans `JwtAuthProvider::register`. Aucune route définie.

- [✅] **13. Gérer les indisponibilités temporaires**
  - *État :* **Manquant**
  - *Détail :* Aucune trace dans le code.

## Actions Prioritaires

1. **[URGENT]** Renommer `app/src/api/actions/ListerCreneauxOccActions.php` en `.Action.php`.
2. Implémenter la route et l'action pour l'inscription (**#12**).
3. Créer la gestion des indisponibilités (**#13**).
4. Clarifier l'accès à l'historique patient (**#11**).
