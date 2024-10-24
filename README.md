# Documentation de l'API - Gestion d'Événements et de Réservations

Cette documentation décrit les contrôleurs et les routes de l'API pour la gestion des utilisateurs, des événements et des réservations.

## Table des Matières

1. [Installation](#installation)
2. [Authentification](#authentification)
3. [Contrôleurs](#contrôleurs)
   - [UserController](#usercontroller)
   - [EventController](#eventcontroller)
   - [BookingController](#bookingcontroller)
4. [Routes API](#routes-api)
5. [Exemples de Requêtes](#exemples-de-requêtes)

## Installation

1. Clonez le dépôt :  
   ```bash
   git clone <url-du-dépôt>
   cd <nom-du-dossier>
   ```

2. Installez les dépendances :  
   ```bash
   composer install
   ```

3. Configurez votre fichier `.env` avec les informations de la base de données.

4. Exécutez les migrations :  
   ```bash
   php artisan migrate
   ```

## Authentification

L'API utilise **Sanctum** pour l'authentification. Vous devez obtenir un token d'accès après la connexion pour accéder aux routes protégées.

### Exemple de connexion
```http
POST /api/login
Content-Type: application/json

{
    "username": "votre_nom_utilisateur",
    "password": "votre_mot_de_passe"
}
```

## Contrôleurs

### UserController

Le `UserController` gère toutes les opérations liées aux utilisateurs.

- **`index()`** : Récupère tous les utilisateurs.
- **`store(UserRequest $request)`** : Crée un nouvel utilisateur.
- **`show($id)`** : Récupère un utilisateur par son ID.
- **`update(UserRequest $request, $id)`** : Met à jour les informations d'un utilisateur.
- **`destroy($id)`** : Supprime un utilisateur.
- **`login(LoginRequest $request)`** : Authentifie un utilisateur et génère un token.
- **`logout(Request $request)`** : Déconnecte l'utilisateur.
- **`resetPassword(Request $request)`** : Réinitialise le mot de passe de l'utilisateur.
- **`getUserId(Request $request)`** : Récupère l'ID de l'utilisateur authentifié.

### EventController

Le `EventController` gère toutes les opérations liées aux événements.

- **`index()`** : Récupère tous les événements.
- **`show($id)`** : Récupère un événement par son ID.
- **`store(EventRequest $request)`** : Crée un nouvel événement.
- **`update(EventRequest $request, $id)`** : Met à jour un événement existant.
- **`destroy($id)`** : Supprime un événement.
- **`getEventByTitle($title)`** : Récupère des événements en fonction de leur titre.

### BookingController

Le `BookingController` gère toutes les opérations liées aux réservations.

- **`index()`** : Récupère toutes les réservations.
- **`store(BookingRequest $request)`** : Crée une nouvelle réservation.
- **`destroy($id)`** : Supprime une réservation.
- **`getUserReservationsById(int $id, Request $request)`** : Récupère les réservations d'un utilisateur par son ID.

## Routes API

Voici les routes définies dans le fichier `api.php` :

```php
Route::middleware('auth:sanctum')->group(function () {
    // Utilisateur
    Route::get('/users', [UserController::class, 'index']);
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
    
    // Événements
    Route::get('/events', [EventController::class, 'index']);
    Route::post('/events', [EventController::class, 'store']);
    Route::get('/events/{id}', [EventController::class, 'show']);
    Route::put('/events/{id}', [EventController::class, 'update']);
    Route::delete('/events/{id}', [EventController::class, 'destroy']);
    Route::get('/events/title/{title}', [EventController::class, 'getEventByTitle']);
    
    // Réservations
    Route::get('/bookings', [BookingController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::delete('/bookings/{id}', [BookingController::class, 'destroy']);
    Route::get('/users/{id}/bookings', [BookingController::class, 'getUserReservationsById']);
});
```

## Exemples de Requêtes

### Exemple de création d'un utilisateur
```http
POST /api/users
Content-Type: application/json

{
    "username": "nouvel_utilisateur",
    "password": "mot_de_passe",
    "email": "utilisateur@example.com",
    "role": "user"
}
```

### Exemple de création d'un événement
```http
POST /api/events
Content-Type: application/json

{
    "title": "Concert",
    "description": "Concert de musique classique",
    "event_date": "2024-12-01",
    "max_attendees": 100,
    "available_seats": 100,
    "lieu": "Salle de concert",
    "image": "lien_de_l_image"
}
```

### Exemple de création d'une réservation
```http
POST /api/bookings
Content-Type: application/json
Authorization: Bearer <votre_token>

{
    "user_id": 1,
    "event_id": 1,
    "number_of_seats": 2
}
```

