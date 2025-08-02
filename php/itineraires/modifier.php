<?php
require_once '../config.php';

$success = $error = '';
$itineraire = null;

// Vérifier si un ID est fourni dans l'URL
if (isset($_GET['id'])) {
    $id = $conn->real_escape_string($_GET['id']);
    
    // Récupérer les informations de l'itinéraire
    $sql = "SELECT * FROM itineraire WHERE codeit = '$id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $itineraire = $result->fetch_assoc();
    } else {
        header("Location: liste.php");
        exit;
    }
} else {
    header("Location: liste.php");
    exit;
}

// Traiter le formulaire de modification
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $villedep = $conn->real_escape_string($_POST['villedep']);
    $villearr = $conn->real_escape_string($_POST['villearr']);
    $frais = (int)$_POST['frais'];
    
    // Mettre à jour l'itinéraire
    $sql = "UPDATE itineraire SET villedep = '$villedep', villearr = '$villearr', frais = $frais WHERE codeit = '$id'";
    
    if ($conn->query($sql) === TRUE) {
        $success = "Itinéraire mis à jour avec succès.";
        // Mettre à jour les informations affichées
        $itineraire['villedep'] = $villedep;
        $itineraire['villearr'] = $villearr;
        $itineraire['frais'] = $frais;
    } else {
        $error = "Erreur lors de la mise à jour: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Itinéraire - Gestion des colis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../../index.php">Coopérative de Transport</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../../index.php">Accueil</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="itineraireDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Itinéraires
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="itineraireDropdown">
                            <li><a class="dropdown-item" href="ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item" href="liste.php">Liste</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="voitureDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Voitures
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="voitureDropdown">
                            <li><a class="dropdown-item" href="../voitures/ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item" href="../voitures/liste.php">Liste</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="envoisDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Envois
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="envoisDropdown">
                            <li><a class="dropdown-item" href="../envois/ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item" href="../envois/liste.php">Liste</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="receptionsDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Réceptions
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="receptionsDropdown">
                            <li><a class="dropdown-item" href="../receptions/ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item" href="../receptions/liste.php">Liste</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="rapportsDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Rapports
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="rapportsDropdown">
                            <li><a class="dropdown-item" href="../rapports/recherche-colis.php">Recherche de colis</a></li>
                            <li><a class="dropdown-item" href="../rapports/recherche-date.php">Recherche par dates</a></li>
                            <li><a class="dropdown-item" href="../rapports/recette-totale.php">Recette totale</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="form-container">
                    <h2 class="mb-4"><i class="fas fa-route me-2"></i>Modifier un itinéraire</h2>
                    
                    <?php
                    if ($success) {
                        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                                $success
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>";
                    }
                    if ($error) {
                        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                                $error
                                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                            </div>";
                    }
                    ?>
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $id); ?>" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="codeit" class="form-label">Code Itinéraire</label>
                            <input type="text" class="form-control" id="codeit" name="codeit" value="<?php echo htmlspecialchars($itineraire['codeit']); ?>" readonly>
                            <div class="form-text">Le code ne peut pas être modifié.</div>
                        </div>
                        <div class="mb-3">
                            <label for="villedep" class="form-label">Ville de Départ</label>
                            <input type="text" class="form-control" id="villedep" name="villedep" value="<?php echo htmlspecialchars($itineraire['villedep']); ?>" required>
                            <div class="invalid-feedback">
                                Veuillez saisir une ville de départ.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="villearr" class="form-label">Ville d'Arrivée</label>
                            <input type="text" class="form-control" id="villearr" name="villearr" value="<?php echo htmlspecialchars($itineraire['villearr']); ?>" required>
                            <div class="invalid-feedback">
                                Veuillez saisir une ville d'arrivée.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="frais" class="form-label">Frais</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="frais" name="frais" min="0" value="<?php echo htmlspecialchars($itineraire['frais']); ?>" required>
                                <span class="input-group-text">Ar</span>
                                <div class="invalid-feedback">
                                    Veuillez saisir un montant valide.
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer</button>
                            <a href="liste.php" class="btn btn-secondary"><i class="fas fa-list me-2"></i>Liste des itinéraires</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light text-center text-lg-start mt-5 py-3">
        <div class="container">
            <p class="text-muted mb-0">© 2025 Coopérative de Transport - Système de Gestion des Colis</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/script.js"></script>
</body>

</html>