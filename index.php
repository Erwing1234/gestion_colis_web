<?php
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des colis d'une coopérative</title>
    <link rel="stylesheet" href="./css/style.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>

    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Coopérative de Transport</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.html">Accueil</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="itineraireDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Itinéraires
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="itineraireDropdown">
                            <li><a class="dropdown-item" href="php/itineraires/ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item" href="php/itineraires/liste.php">Liste</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="voitureDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Voitures
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="voitureDropdown">
                            <li><a class="dropdown-item" href="php/voitures/ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item" href="php/voitures/liste.php">Liste</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="envoisDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Envois
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="envoisDropdown">
                            <li><a class="dropdown-item" href="php/envois/ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item" href="php/envois/liste.php">Liste</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="receptionsDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Réceptions
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="receptionsDropdown">
                            <li><a class="dropdown-item" href="php/receptions/ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item" href="php/receptions/liste.php">Liste</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="rapportsDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Rapports
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="rapportsDropdown">
                            <li><a class="dropdown-item" href="php/rapports/recherche-colis.php">Recherche de colis</a></li>
                            <li><a class="dropdown-item" href="php/rapports/recherche-date.php">Recherche par dates</a></li>
                            <li><a class="dropdown-item" href="php/rapports/recette-totale.php">Recette totale</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="jumbotron">
            <h1 class="display-4">Gestion des colis d'une coopérative</h1>
            <p class="lead">Système de gestion pour le suivi des colis, des itinéraires, des véhicules et des réceptions.</p>
            <hr class="my-4">
        </div>

        <div class="row mt-5">
            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-route fa-4x mb-3 text-primary"></i>
                        <h5 class="card-title">Itinéraires</h5>
                        <p class="card-text">Gérer les routes et destinations</p>
                        <a href="php/itineraires/liste.php" class="btn btn-primary">Accéder</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-car fa-4x mb-3 text-success"></i>
                        <h5 class="card-title">Voitures</h5>
                        <p class="card-text">Gérer les véhicules disponibles</p>
                        <a href="php/voitures/liste.php" class="btn btn-success">Accéder</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-paper-plane fa-4x mb-3 text-info"></i>
                        <h5 class="card-title">Envois</h5>
                        <p class="card-text">Gérer les colis envoyés</p>
                        <a href="php/envois/liste.php" class="btn btn-info">Accéder</a>
                    </div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-inbox fa-4x mb-3 text-warning"></i>
                        <h5 class="card-title">Réceptions</h5>
                        <p class="card-text">Gérer les colis reçus</p>
                        <a href="php/receptions/liste.php" class="btn btn-warning">Accéder</a>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-search fa-4x mb-3 text-danger"></i>
                        <h5 class="card-title">Rechercher colis</h5>
                        <p class="card-text">Rechercher un colis par code ou désignation</p>
                        <a href="php/rapports/recherche-colis.php" class="btn btn-danger">Accéder</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-calendar-alt fa-4x mb-3 text-secondary"></i>
                        <h5 class="card-title">Recherche par dates</h5>
                        <p class="card-text">Rechercher les colis entre deux dates</p>
                        <a href="php/rapports/recherche-date.php" class="btn btn-secondary">Accéder</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-body text-center">
                        <i class="fas fa-money-bill-wave fa-4x mb-3 text-dark"></i>
                        <h5 class="card-title">Recette totale</h5>
                        <p class="card-text">Calculer la recette totale</p>
                        <a href="php/rapports/recette-totale.php" class="btn btn-dark">Accéder</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light text-center text-lg-start mt-5 py-3">
        <div class="container">
            <p class="text-muted mb-0">© 2025 Coopérative de Transport - Système de Gestion des Colis</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./js/script.js"></script>
    
    </body>
</html>