<?php
require_once '../config.php';

$message = '';
$results = null;
$date_debut = '';
$date_fin = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['date_debut']) && isset($_POST['date_fin']) && !empty($_POST['date_debut']) && !empty($_POST['date_fin'])) {
        $date_debut = $conn->real_escape_string($_POST['date_debut']);
        $date_fin = $conn->real_escape_string($_POST['date_fin']);
        
        // Convertir les dates pour inclure l'heure
        $date_debut_formatted = $date_debut . ' 00:00:00';
        $date_fin_formatted = $date_fin . ' 23:59:59';
        
        // Recherche des colis entre deux dates
        $sql = "SELECT e.*, v.design AS voiture_design, i.villedep, i.villearr, 
                      r.idrecept, r.date_recept
                FROM envoyer e
                JOIN voiture v ON e.idvoit = v.idvoit
                JOIN itineraire i ON v.codeit = i.codeit
                LEFT JOIN recevoir r ON e.idenvoi = r.idenvoi
                WHERE e.date_envoi BETWEEN '$date_debut_formatted' AND '$date_fin_formatted'
                ORDER BY e.date_envoi DESC";
        
        $results = $conn->query($sql);
        
        if ($results->num_rows === 0) {
            $message = "<div class='alert alert-info'>Aucun colis trouvé entre le " . htmlspecialchars($date_debut) . " et le " . htmlspecialchars($date_fin) . ".</div>";
        } else {
            $message = "<div class='alert alert-success'>" . $results->num_rows . " colis trouvés entre le " . htmlspecialchars($date_debut) . " et le " . htmlspecialchars($date_fin) . ".</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Veuillez sélectionner une période de recherche.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche par Dates - Gestion des colis</title>
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
                        <a class="nav-link dropdown-toggle active" href="#" id="rapportsDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Rapports
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="rapportsDropdown">
                            <li><a class="dropdown-item" href="recherche-colis.php">Recherche de colis</a></li>
                            <li><a class="dropdown-item active" href="recherche-date.php">Recherche par dates</a></li>
                            <li><a class="dropdown-item" href="recette-totale.php">Recette totale</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="search-form">
                    <h2 class="mb-4"><i class="fas fa-calendar-alt me-2"></i>Recherche par dates</h2>
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="searchForm">
                        <div class="row">
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="date_debut" class="form-label">Date de début</label>
                                    <input type="date" class="form-control" id="date_debut" name="date_debut" 
                                        value="<?php echo $date_debut; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="mb-3">
                                    <label for="date_fin" class="form-label">Date de fin</label>
                                    <input type="date" class="form-control" id="date_fin" name="date_fin" 
                                        value="<?php echo $date_fin; ?>" required>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="mb-3 w-100">
                                    <button class="btn btn-primary w-100" type="submit"><i class="fas fa-search me-2"></i>Rechercher</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <p class="text-muted">
                        <small>Sélectionnez une période pour afficher tous les colis envoyés durant cette période.</small>
                    </p>
                </div>
                
                <?php echo $message; ?>
                
                <?php if ($results && $results->num_rows > 0): ?>
                <div class="table-container mt-4">
                    <h3>Résultats de la recherche</h3>
                    
                    <?php
                    // Calcul des statistiques
                    $total_colis = $results->num_rows;
                    $colis_recus = 0;
                    $colis_transit = 0;
                    $total_frais = 0;
                    
                    // Copier les résultats car on va parcourir deux fois
                    $rows = [];
                    while($row = $results->fetch_assoc()) {
                        $rows[] = $row;
                        $total_frais += $row['frais'];
                        if ($row['idrecept']) {
                            $colis_recus++;
                        } else {
                            $colis_transit++;
                        }
                    }
                    ?>
                    
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-light">
                                <div class="card-body text-center">
                                    <h4><?php echo $total_colis; ?></h4>
                                    <p class="mb-0">Total des colis</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h4><?php echo $colis_recus; ?></h4>
                                    <p class="mb-0">Colis reçus</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark">
                                <div class="card-body text-center">
                                    <h4><?php echo $colis_transit; ?></h4>
                                    <p class="mb-0">Colis en transit</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h4><?php echo number_format($total_frais, 0, ',', ' '); ?> Ar</h4>
                                    <p class="mb-0">Total des frais</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Date d'envoi</th>
                                    <th>Description</th>
                                    <th>Statut</th>
                                    <th>Date de réception</th>
                                    <th>Itinéraire</th>
                                    <th>Expéditeur</th>
                                    <th>Destinataire</th>
                                    <th>Frais (Ar)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach($rows as $row) {
                                    echo "<tr>";
                                    echo "<td>" . $row['idenvoi'] . "</td>";
                                    echo "<td>" . date('d/m/Y H:i', strtotime($row['date_envoi'])) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['colis']) . "</td>";
                                    
                                    // Affichage du statut
                                    if ($row['idrecept']) {
                                        echo "<td><span class='badge bg-success'>Reçu</span></td>";
                                        echo "<td>" . date('d/m/Y H:i', strtotime($row['date_recept'])) . "</td>";
                                    } else {
                                        echo "<td><span class='badge bg-warning'>En transit</span></td>";
                                        echo "<td>-</td>";
                                    }
                                    
                                    echo "<td>" . htmlspecialchars($row['villedep'] . ' - ' . $row['villearr']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nomEnvoyeur']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nomRecepteur'] . '<br>' . $row['contactRecepteur']) . "</td>";
                                    echo "<td>" . number_format($row['frais'], 0, ',', ' ') . "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <td colspan="8" class="text-end"><strong>Total</strong></td>
                                    <td><strong><?php echo number_format($total_frais, 0, ',', ' '); ?> Ar</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <div class="text-center mt-4">
                        <button class="btn btn-info" onclick="window.print()"><i class="fas fa-print me-2"></i>Imprimer ce rapport</button>
                    </div>
                </div>
                <?php endif; ?>
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