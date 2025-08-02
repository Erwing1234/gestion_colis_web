<?php
require_once '../config.php';

$message = '';

// Supprimer une voiture
if (isset($_GET['delete'])) {
    $id = $conn->real_escape_string($_GET['delete']);
    
    // Vérifier si la voiture est utilisée dans un envoi
    $check_envoi = "SELECT idenvoi FROM envoyer WHERE idvoit = '$id'";
    $result_envoi = $conn->query($check_envoi);
    
    if ($result_envoi->num_rows > 0) {
        $message = "<div class='alert alert-danger'>Impossible de supprimer cette voiture car elle est associée à des envois de colis.</div>";
    } else {
        $sql = "DELETE FROM voiture WHERE idvoit = '$id'";
        
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success'>La voiture a été supprimée avec succès.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Erreur lors de la suppression: " . $conn->error . "</div>";
        }
    }
}

// Récupérer la liste des voitures avec les informations d'itinéraire
$sql = "SELECT v.idvoit, v.design, v.codeit, i.villedep, i.villearr, i.frais 
        FROM voiture v 
        JOIN itineraire i ON v.codeit = i.codeit 
        ORDER BY v.idvoit";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Voitures - Gestion des colis</title>
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
                            <li><a class="dropdown-item" href="ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item active" href="liste.php">Liste</a></li>
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
            <div class="col-md-12">
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-car me-2"></i>Liste des voitures</h2>
                        <a href="ajouter.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Ajouter une voiture</a>
                    </div>
                    
                    <?php echo $message; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Désignation</th>
                                    <th>Code Itinéraire</th>
                                    <th>Itinéraire</th>
                                    <th>Frais (Ar)</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($row['idvoit']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['design']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['codeit']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['villedep']) . " - " . htmlspecialchars($row['villearr']) . "</td>";
                                        echo "<td>" . number_format($row['frais'], 0, ',', ' ') . "</td>";
                                        echo "<td>
                                                <a href='modifier.php?id=" . $row['idvoit'] . "' class='btn btn-sm btn-warning me-1' data-bs-toggle='tooltip' title='Modifier'><i class='fas fa-edit'></i></a>
                                                <a href='liste.php?delete=" . $row['idvoit'] . "' class='btn btn-sm btn-danger btn-delete' data-bs-toggle='tooltip' title='Supprimer'><i class='fas fa-trash'></i></a>
                                              </td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' class='text-center'>Aucune voiture trouvée.</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/script.js"></script>
</body>

</html>