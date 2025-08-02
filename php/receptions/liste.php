<?php
require_once '../config.php';

$message = '';

// Récupérer la liste des réceptions
$sql = "SELECT r.idrecept, r.date_recept, e.idenvoi, e.colis, e.date_envoi, e.nomEnvoyeur, e.emailEnvoyeur, e.nomRecepteur, e.contactRecepteur, e.frais, v.idvoit, v.design, i.villedep, i.villearr
        FROM recevoir r
        JOIN envoyer e ON r.idenvoi = e.idenvoi
        JOIN voiture v ON e.idvoit = v.idvoit
        JOIN itineraire i ON v.codeit = i.codeit
        ORDER BY r.date_recept DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Réceptions - Gestion des colis</title>
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
                        <a class="nav-link dropdown-toggle active" href="#" id="receptionsDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            Réceptions
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="receptionsDropdown">
                            <li><a class="dropdown-item" href="ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item active" href="liste.php">Liste</a></li>
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
                        <h2><i class="fas fa-inbox me-2"></i>Liste des réceptions de colis</h2>
                        <a href="ajouter.php" class="btn btn-primary"><i class="fas fa-plus me-2"></i>Ajouter une réception</a>
                    </div>
                    
                    <?php echo $message; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID Réception</th>
                                    <th>Date de Réception</th>
                                    <th>ID Envoi</th>
                                    <th>Date d'Envoi</th>
                                    <th>Délai</th>
                                    <th>Itinéraire</th>
                                    <th>Colis</th>
                                    <th>Expéditeur</th>
                                    <th>Destinataire</th>
                                    <th>Frais (Ar)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        // Calculer le délai entre l'envoi et la réception
                                        $date_envoi = new DateTime($row['date_envoi']);
                                        $date_recept = new DateTime($row['date_recept']);
                                        $difference = $date_recept->diff($date_envoi);
                                        
                                        // Formater le délai
                                        if ($difference->days > 0) {
                                            $delai = $difference->format('%a jours, %h heures');
                                        } else {
                                            $delai = $difference->format('%h heures, %i minutes');
                                        }
                                        
                                        echo "<tr>";
                                        echo "<td>" . $row['idrecept'] . "</td>";
                                        echo "<td>" . date('d/m/Y H:i', strtotime($row['date_recept'])) . "</td>";
                                        echo "<td>" . $row['idenvoi'] . "</td>";
                                        echo "<td>" . date('d/m/Y H:i', strtotime($row['date_envoi'])) . "</td>";
                                        echo "<td>" . $delai . "</td>";
                                        echo "<td>" . htmlspecialchars($row['villedep'] . ' - ' . $row['villearr']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['colis']) . "</td>";
                                        echo "<td>" . htmlspecialchars($row['nomEnvoyeur']) . "<br><small>" . htmlspecialchars($row['emailEnvoyeur']) . "</small></td>";
                                        echo "<td>" . htmlspecialchars($row['nomRecepteur']) . "<br><small>" . htmlspecialchars($row['contactRecepteur']) . "</small></td>";
                                        echo "<td>" . number_format($row['frais'], 0, ',', ' ') . "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10' class='text-center'>Aucune réception trouvée.</td></tr>";
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