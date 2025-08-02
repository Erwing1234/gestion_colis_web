<?php
require_once '../config.php';

$success = $error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idvoit = $conn->real_escape_string($_POST['idvoit']);
    $design = $conn->real_escape_string($_POST['design']);
    $codeit = $conn->real_escape_string($_POST['codeit']);
    
    // Vérifier si l'ID de voiture existe déjà
    $check_sql = "SELECT idvoit FROM voiture WHERE idvoit = '$idvoit'";
    $result = $conn->query($check_sql);
    
    if ($result->num_rows > 0) {
        $error = "Cet identifiant de voiture existe déjà.";
    } else {
        // Insérer la voiture
        $sql = "INSERT INTO voiture (idvoit, design, codeit) VALUES ('$idvoit', '$design', '$codeit')";
        
        if ($conn->query($sql) === TRUE) {
            $success = "Voiture ajoutée avec succès.";
        } else {
            $error = "Erreur: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Récupérer tous les itinéraires pour le menu déroulant
$sql_itineraires = "SELECT codeit, villedep, villearr FROM itineraire ORDER BY codeit";
$result_itineraires = $conn->query($sql_itineraires);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter une Voiture - Gestion des colis</title>
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
                        <a class="nav-link dropdown-toggle" href="#" id="itineraireDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Itinéraires
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="itineraireDropdown">
                            <li><a class="dropdown-item" href="../itineraires/ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item" href="../itineraires/liste.php">Liste</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" id="voitureDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Voitures
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="voitureDropdown">
                            <li><a class="dropdown-item active" href="ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item" href="liste.php">Liste</a></li>
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
                    <h2 class="mb-4"><i class="fas fa-car me-2"></i>Ajouter une voiture</h2>
                    
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
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="idvoit" class="form-label">ID Voiture</label>
                            <input type="text" class="form-control" id="idvoit" name="idvoit" required>
                            <div class="invalid-feedback">
                                Veuillez saisir un identifiant pour la voiture.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="design" class="form-label">Désignation</label>
                            <input type="text" class="form-control" id="design" name="design" required>
                            <div class="invalid-feedback">
                                Veuillez saisir une désignation pour la voiture.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="codeit" class="form-label">Itinéraire</label>
                            <select class="form-select" id="codeit" name="codeit" required>
                                <option value="">Sélectionner un itinéraire</option>
                                <?php
                                if ($result_itineraires->num_rows > 0) {
                                    while($row = $result_itineraires->fetch_assoc()) {
                                        echo "<option value='" . $row['codeit'] . "'>" . $row['codeit'] . " - " . $row['villedep'] . " à " . $row['villearr'] . "</option>";
                                    }
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">
                                Veuillez sélectionner un itinéraire.
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer</button>
                            <a href="liste.php" class="btn btn-secondary"><i class="fas fa-list me-2"></i>Liste des voitures</a>
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