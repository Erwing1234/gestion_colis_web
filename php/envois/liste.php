<?php
require_once '../config.php';

$message = '';

// Supprimer un envoi
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    
    // Vérifier si l'envoi est associé à une réception
    $check_reception = "SELECT idrecept FROM recevoir WHERE idenvoi = $id";
    $result_reception = $conn->query($check_reception);
    
    if ($result_reception->num_rows > 0) {
        $message = "<div class='alert alert-danger'>Impossible de supprimer cet envoi car il est associé à une réception.</div>";
    } else {
        $sql = "DELETE FROM envoyer WHERE idenvoi = $id";
        
        if ($conn->query($sql) === TRUE) {
            $message = "<div class='alert alert-success'>L'envoi a été supprimé avec succès.</div>";
        } else {
            $message = "<div class='alert alert-danger'>Erreur lors de la suppression: " . $conn->error . "</div>";
        }
    }
}

// Récupérer la liste des envois
$sql = "SELECT e.*, v.design, i.villedep, i.villearr, r.idrecept 
        FROM envoyer e
        JOIN voiture v ON e.idvoit = v.idvoit
        JOIN itineraire i ON v.codeit = i.codeit
        LEFT JOIN recevoir r ON e.idenvoi = r.idenvoi
        ORDER BY e.date_envoi DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Envois - Gestion des colis</title>
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
                        <a class="nav-link dropdown-toggle active" href="#" id="envoisDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Envois
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="envoisDropdown">
                            <li><a class="dropdown-item" href="ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item active" href="liste.php">Liste</a></li>
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

    <div class="container-fluid mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="table-container">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2><i class="fas fa-paper-plane me-2"></i>Liste des envois de colis</h2>
                        <a href="ajouter.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Ajouter un envoi</a>
                    </div>
                    
                    <?php echo $message; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date d'envoi</th>
                                    <th>Voiture</th>
                                    <th>Itinéraire</th>
                                    <th>Colis</th>
                                    <th>Expéditeur</th>
                                    <th>Destinataire</th>
                                    <th>Frais (Ar)</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        echo "<tr>";
                                        echo "<td>" . $row['idenvoi'] . "</td>";
                                        echo "<td>" . date('d/m/Y H:i', strtotime($row['date_envoi'])) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['idvoit'] . ' - ' . $row['design']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['villedep'] . ' - ' . $row['villearr']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['colis']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nomEnvoyeur']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nomRecepteur'] . '<br>' . $row['contactRecepteur']) . "</td>";
                                        echo "<td>" . number_format($row['frais'], 0, ',', ' ') . "</td>";
                                        
                                        // Statut
                                        if ($row['idrecept']) {
                                            echo "<td><span class='badge bg-success'>Reçu</span></td>";
                                        } else {
                                            echo "<td><span class='badge bg-warning'>En transit</span></td>";
                                        }
                                        
                                        // Actions
                                        echo "<td>";
                                        echo "<a href='recu.php?id=" . $row['idenvoi'] . "' class='btn btn-sm btn-info me-1' data-bs-toggle='tooltip' title='Voir le reçu' target='_blank'><i class='fas fa-file-alt'></i></a>";
                                        
                                        // Si le colis n'est pas encore reçu, on peut le modifier ou le supprimer
                                        if (!$row['idrecept']) {
                                            echo "<a href='modifier.php?id=" . $row['idenvoi'] . "' class='btn btn-sm btn-warning me-1' data-bs-toggle='tooltip' title='Modifier'><i class='fas fa-edit'></i></a>";
                                            echo "<a href='liste.php?delete=" . $row['idenvoi'] . "' class='btn btn-sm btn-danger btn-delete' data-bs-toggle='tooltip' title='Supprimer'><i class='fas fa-trash'></i></a>";
                                        }
                                        // Si le colis n'est pas reçu, proposer de le marquer comme reçu
                                        if (!$row['idrecept']) {
                                            echo "<a href='../receptions/ajouter.php?id=" . $row['idenvoi'] . "' class='btn btn-sm btn-success ms-1' data-bs-toggle='tooltip' title='Marquer comme reçu'><i class='fas fa-check'></i></a>";
                                        }
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10' class='text-center'>Aucun envoi trouvé.</td></tr>";
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