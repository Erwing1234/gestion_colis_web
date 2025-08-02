<?php
require_once '../config.php';

$message = '';
$results = null;
$search_term = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['search']) && !empty($_POST['search'])) {
        $search_term = $conn->real_escape_string($_POST['search']);
        
        // Recherche d'un colis par son code d'envoi ou sa désignation avec LIKE
        $sql = "SELECT e.*, v.design AS voiture_design, i.villedep, i.villearr, 
                      r.idrecept, r.date_recept
                FROM envoyer e
                JOIN voiture v ON e.idvoit = v.idvoit
                JOIN itineraire i ON v.codeit = i.codeit
                LEFT JOIN recevoir r ON e.idenvoi = r.idenvoi
                WHERE e.idenvoi LIKE '%$search_term%' OR e.colis LIKE '%$search_term%'
                ORDER BY e.date_envoi DESC";
        
        $results = $conn->query($sql);
        
        if ($results->num_rows === 0) {
            $message = "<div class='alert alert-info'>Aucun résultat trouvé pour: \"" . htmlspecialchars($search_term) . "\"</div>";
        }
    } else {
        $message = "<div class='alert alert-warning'>Veuillez saisir un terme de recherche.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche de Colis - Gestion des colis</title>
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
                            <li><a class="dropdown-item active" href="recherche-colis.php">Recherche de colis</a></li>
                            <li><a class="dropdown-item" href="recherche-date.php">Recherche par dates</a></li>
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
                    <h2 class="mb-4"><i class="fas fa-search me-2"></i>Recherche de colis</h2>
                    
                    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" id="searchForm">
                        <div class="input-group mb-3">
                            <input type="text" class="form-control form-control-lg" id="search" name="search" 
                                placeholder="Rechercher par ID ou description du colis..." 
                                value="<?php echo htmlspecialchars($search_term); ?>">
                            <button class="btn btn-primary" type="submit"><i class="fas fa-search me-2"></i>Rechercher</button>
                        </div>
                    </form>
                    
                    <p class="text-muted">
                        <small>Vous pouvez rechercher un colis par son numéro d'ID ou sa description.</small>
                    </p>
                </div>
                
                <?php echo $message; ?>
                
                <?php if ($results && $results->num_rows > 0): ?>
                <div class="table-container mt-4">
                    <h3>Résultats de la recherche</h3>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Description</th>
                                    <th>Statut</th>
                                    <th>Date d'envoi</th>
                                    <th>Date de réception</th>
                                    <th>Itinéraire</th>
                                    <th>Expéditeur</th>
                                    <th>Destinataire</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                while($row = $results->fetch_assoc()) {
                                    echo "<tr>";
                                    echo "<td>" . $row['idenvoi'] . "</td>";
                                    echo "<td>" . htmlspecialchars($row['colis']) . "</td>";
                                    
                                    // Affichage du statut
                                    if ($row['idrecept']) {
                                        echo "<td><span class='badge bg-success'>Reçu</span></td>";
                                    } else {
                                        echo "<td><span class='badge bg-warning'>En transit</span></td>";
                                    }
                                    
                                    echo "<td>" . date('d/m/Y H:i', strtotime($row['date_envoi'])) . "</td>";
                                    echo "<td>" . ($row['date_recept'] ? date('d/m/Y H:i', strtotime($row['date_recept'])) : '-') . "</td>";
                                    echo "<td>" . htmlspecialchars($row['villedep'] . ' - ' . $row['villearr']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nomEnvoyeur']) . "</td>";
                                    echo "<td>" . htmlspecialchars($row['nomRecepteur'] . '<br>' . $row['contactRecepteur']) . "</td>";
                                    
                                    // Actions
                                    echo "<td>";
                                    echo "<a href='../envois/recu.php?id=" . $row['idenvoi'] . "' class='btn btn-sm btn-info me-1' data-bs-toggle='tooltip' title='Voir le reçu' target='_blank'><i class='fas fa-file-alt'></i></a>";
                                    
                                    // Si le colis n'est pas encore reçu, proposer de le marquer comme reçu
                                    if (!$row['idrecept']) {
                                        echo "<a href='../receptions/ajouter.php?id=" . $row['idenvoi'] . "' class='btn btn-sm btn-success ms-1' data-bs-toggle='tooltip' title='Marquer comme reçu'><i class='fas fa-check'></i></a>";
                                    }
                                    echo "</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
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