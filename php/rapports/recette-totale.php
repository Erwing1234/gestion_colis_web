<?php
require_once '../config.php';

// Définir les variables de filtre
$filter_period = isset($_GET['period']) ? $_GET['period'] : 'all';
$filter_itineraire = isset($_GET['itineraire']) ? $_GET['itineraire'] : 'all';
$filter_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
$filter_month = isset($_GET['month']) ? (int)$_GET['month'] : date('m');

// Récupérer la liste des années pour le filtre
$sql_years = "SELECT DISTINCT YEAR(date_envoi) as year FROM envoyer ORDER BY year DESC";
$result_years = $conn->query($sql_years);
$years = [];
if ($result_years->num_rows > 0) {
    while($row = $result_years->fetch_assoc()) {
        $years[] = $row['year'];
    }
}

// Récupérer la liste des itinéraires pour le filtre
$sql_itineraires = "SELECT codeit, villedep, villearr FROM itineraire ORDER BY villedep, villearr";
$result_itineraires = $conn->query($sql_itineraires);

// Construire la requête SQL en fonction des filtres
$sql_condition = "1=1"; // condition de base toujours vraie

// Filtre par période
if ($filter_period == 'month') {
    $sql_condition .= " AND YEAR(e.date_envoi) = $filter_year AND MONTH(e.date_envoi) = $filter_month";
} elseif ($filter_period == 'year') {
    $sql_condition .= " AND YEAR(e.date_envoi) = $filter_year";
}

// Filtre par itinéraire
if ($filter_itineraire != 'all') {
    $sql_condition .= " AND i.codeit = " . (int)$filter_itineraire;
}

// Requête pour les données générales
$sql_general = "SELECT 
                COUNT(e.idenvoi) as total_colis,
                SUM(e.frais) as total_frais,
                COUNT(r.idrecept) as colis_recus,
                (COUNT(e.idenvoi) - COUNT(r.idrecept)) as colis_transit
               FROM envoyer e
               LEFT JOIN recevoir r ON e.idenvoi = r.idenvoi
               JOIN voiture v ON e.idvoit = v.idvoit
               JOIN itineraire i ON v.codeit = i.codeit
               WHERE $sql_condition";

$result_general = $conn->query($sql_general);
$general_data = $result_general->fetch_assoc();

// Requête pour les recettes par itinéraire
$sql_recettes_itineraire = "SELECT 
                           i.codeit,
                           i.villedep,
                           i.villearr,
                           COUNT(e.idenvoi) as nb_colis,
                           SUM(e.frais) as total_frais,
                           COUNT(r.idrecept) as colis_recus
                          FROM envoyer e
                          JOIN voiture v ON e.idvoit = v.idvoit
                          JOIN itineraire i ON v.codeit = i.codeit
                          LEFT JOIN recevoir r ON e.idenvoi = r.idenvoi
                          WHERE $sql_condition
                          GROUP BY i.codeit, i.villedep, i.villearr
                          ORDER BY total_frais DESC";

$result_recettes_itineraire = $conn->query($sql_recettes_itineraire);

// Requête pour les recettes par mois (si filtre annuel)
$sql_recettes_mois = "";
$result_recettes_mois = null;

if ($filter_period == 'year') {
    $sql_recettes_mois = "SELECT 
                         MONTH(e.date_envoi) as mois,
                         COUNT(e.idenvoi) as nb_colis,
                         SUM(e.frais) as total_frais
                        FROM envoyer e
                        JOIN voiture v ON e.idvoit = v.idvoit
                        JOIN itineraire i ON v.codeit = i.codeit
                        WHERE YEAR(e.date_envoi) = $filter_year " . 
                        ($filter_itineraire != 'all' ? "AND i.codeit = " . (int)$filter_itineraire : "") . "
                        GROUP BY MONTH(e.date_envoi)
                        ORDER BY mois";
    
    $result_recettes_mois = $conn->query($sql_recettes_mois);
}

// Noms des mois en français
$mois_fr = [
    1 => 'Janvier', 2 => 'Février', 3 => 'Mars', 4 => 'Avril',
    5 => 'Mai', 6 => 'Juin', 7 => 'Juillet', 8 => 'Août',
    9 => 'Septembre', 10 => 'Octobre', 11 => 'Novembre', 12 => 'Décembre'
];

// Titre du rapport
$titre_rapport = "Rapport des recettes ";
if ($filter_period == 'all') {
    $titre_rapport .= "globales";
} elseif ($filter_period == 'year') {
    $titre_rapport .= "de l'année " . $filter_year;
} elseif ($filter_period == 'month') {
    $titre_rapport .= "du mois de " . $mois_fr[$filter_month] . " " . $filter_year;
}

if ($filter_itineraire != 'all') {
    $sql_get_itineraire = "SELECT villedep, villearr FROM itineraire WHERE codeit = " . (int)$filter_itineraire;
    $result_get_itineraire = $conn->query($sql_get_itineraire);
    if ($result_get_itineraire->num_rows > 0) {
        $itineraire_info = $result_get_itineraire->fetch_assoc();
        $titre_rapport .= " - Itinéraire : " . $itineraire_info['villedep'] . " - " . $itineraire_info['villearr'];
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recette Totale - Gestion des colis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        @media print {
            .no-print {
                display: none !important;
            }
            .container {
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary no-print">
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
                            <li><a class="dropdown-item" href="recherche-date.php">Recherche par dates</a></li>
                            <li><a class="dropdown-item active" href="recette-totale.php">Recette totale</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2><i class="fas fa-chart-line me-2"></i>Rapport des recettes</h2>
                    <div class="btn-group no-print">
                        <button class="btn btn-info" onclick="window.print()"><i class="fas fa-print me-2"></i>Imprimer ce rapport</button>
                    </div>
                </div>

                <!-- Formulaire de filtrage -->
                <form action="recette-totale.php" method="get" class="bg-light p-3 mb-4 rounded no-print">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="period" class="form-label">Période</label>
                            <select class="form-select" id="period" name="period" onchange="togglePeriodOptions()">
                                <option value="all" <?php echo $filter_period == 'all' ? 'selected' : ''; ?>>Toutes les périodes</option>
                                <option value="year" <?php echo $filter_period == 'year' ? 'selected' : ''; ?>>Année spécifique</option>
                                <option value="month" <?php echo $filter_period == 'month' ? 'selected' : ''; ?>>Mois spécifique</option>
                            </select>
                        </div>
                        
                        <div class="col-md-2" id="yearDiv" style="<?php echo ($filter_period == 'all') ? 'display:none;' : ''; ?>">
                            <label for="year" class="form-label">Année</label>
                            <select class="form-select" id="year" name="year">
                                <?php foreach ($years as $year): ?>
                                <option value="<?php echo $year; ?>" <?php echo $year == $filter_year ? 'selected' : ''; ?>><?php echo $year; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-2" id="monthDiv" style="<?php echo ($filter_period != 'month') ? 'display:none;' : ''; ?>">
                            <label for="month" class="form-label">Mois</label>
                            <select class="form-select" id="month" name="month">
                                <?php for($i = 1; $i <= 12; $i++): ?>
                                <option value="<?php echo $i; ?>" <?php echo $i == $filter_month ? 'selected' : ''; ?>><?php echo $mois_fr[$i]; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="itineraire" class="form-label">Itinéraire</label>
                            <select class="form-select" id="itineraire" name="itineraire">
                                <option value="all">Tous les itinéraires</option>
                                <?php
                                if ($result_itineraires->num_rows > 0) {
                                    while($row = $result_itineraires->fetch_assoc()) {
                                        $selected = ($filter_itineraire == $row['codeit']) ? 'selected' : '';
                                        echo "<option value='" . $row['codeit'] . "' $selected>" . 
                                              htmlspecialchars($row['villedep'] . ' - ' . $row['villearr']) . "</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        
                        <div class="col-md-12 text-end">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-2"></i>Filtrer</button>
                            <a href="recette-totale.php" class="btn btn-secondary"><i class="fas fa-undo me-2"></i>Réinitialiser</a>
                        </div>
                    </div>
                </form>
                
                <div class="mt-4 mb-3">
                    <h3 class="text-center"><?php echo $titre_rapport; ?></h3>
                    <p class="text-center text-muted">Date du rapport: <?php echo date('d/m/Y'); ?></p>
                </div>
                
                <!-- Carte récapitulative -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-light">
                            <div class="card-body text-center">
                                <h4><?php echo $general_data['total_colis']; ?></h4>
                                <p class="mb-0">Total des colis</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h4><?php echo $general_data['colis_recus']; ?></h4>
                                <p class="mb-0">Colis reçus</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-dark">
                            <div class="card-body text-center">
                                <h4><?php echo $general_data['colis_transit']; ?></h4>
                                <p class="mb-0">Colis en transit</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h4><?php echo number_format($general_data['total_frais'], 0, ',', ' '); ?> Ar</h4>
                                <p class="mb-0">Total des frais</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tableau des recettes par itinéraire -->
                <?php if ($result_recettes_itineraire->num_rows > 0): ?>
                <div class="mb-5">
                    <h4>Recettes par itinéraire</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Itinéraire</th>
                                    <th class="text-center">Nombre de colis</th>
                                    <th class="text-center">Colis reçus</th>
                                    <th class="text-center">Colis en transit</th>
                                    <th class="text-end">Total des frais</th>
                                    <th class="text-end">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                while($row = $result_recettes_itineraire->fetch_assoc()) {
                                    $pourcentage = ($general_data['total_frais'] > 0) ? 
                                                 round(($row['total_frais'] / $general_data['total_frais']) * 100, 1) : 0;
                                    echo "<tr>";
                                    echo "<td>" . htmlspecialchars($row['villedep'] . ' - ' . $row['villearr']) . "</td>";
                                    echo "<td class='text-center'>" . $row['nb_colis'] . "</td>";
                                    echo "<td class='text-center'>" . $row['colis_recus'] . "</td>";
                                    echo "<td class='text-center'>" . ($row['nb_colis'] - $row['colis_recus']) . "</td>";
                                    echo "<td class='text-end'>" . number_format($row['total_frais'], 0, ',', ' ') . " Ar</td>";
                                    echo "<td class='text-end'>" . $pourcentage . "%</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <td><strong>Total</strong></td>
                                    <td class="text-center"><strong><?php echo $general_data['total_colis']; ?></strong></td>
                                    <td class="text-center"><strong><?php echo $general_data['colis_recus']; ?></strong></td>
                                    <td class="text-center"><strong><?php echo $general_data['colis_transit']; ?></strong></td>
                                    <td class="text-end"><strong><?php echo number_format($general_data['total_frais'], 0, ',', ' '); ?> Ar</strong></td>
                                    <td class="text-end"><strong>100%</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Tableau des recettes par mois (si filtre annuel) -->
                <?php if ($filter_period == 'year' && $result_recettes_mois && $result_recettes_mois->num_rows > 0): ?>
                <div class="mb-5">
                    <h4>Recettes par mois pour l'année <?php echo $filter_year; ?></h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Mois</th>
                                    <th class="text-center">Nombre de colis</th>
                                    <th class="text-end">Total des frais</th>
                                    <th class="text-end">%</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                while($row = $result_recettes_mois->fetch_assoc()) {
                                    $pourcentage = ($general_data['total_frais'] > 0) ? 
                                                 round(($row['total_frais'] / $general_data['total_frais']) * 100, 1) : 0;
                                    echo "<tr>";
                                    echo "<td>" . $mois_fr[$row['mois']] . "</td>";
                                    echo "<td class='text-center'>" . $row['nb_colis'] . "</td>";
                                    echo "<td class='text-end'>" . number_format($row['total_frais'], 0, ',', ' ') . " Ar</td>";
                                    echo "<td class='text-end'>" . $pourcentage . "%</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-dark">
                                    <td><strong>Total</strong></td>
                                    <td class="text-center"><strong><?php echo $general_data['total_colis']; ?></strong></td>
                                    <td class="text-end"><strong><?php echo number_format($general_data['total_frais'], 0, ',', ' '); ?> Ar</strong></td>
                                    <td class="text-end"><strong>100%</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <footer class="bg-light text-center text-lg-start mt-5 py-3 no-print">
        <div class="container">
            <p class="text-muted mb-0">© 2025 Coopérative de Transport - Système de Gestion des Colis</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/script.js"></script>
    <script>
        function togglePeriodOptions() {
            const periodSelect = document.getElementById('period');
            const yearDiv = document.getElementById('yearDiv');
            const monthDiv = document.getElementById('monthDiv');
            
            if (periodSelect.value === 'all') {
                yearDiv.style.display = 'none';
                monthDiv.style.display = 'none';
            } else if (periodSelect.value === 'year') {
                yearDiv.style.display = 'block';
                monthDiv.style.display = 'none';
            } else if (periodSelect.value === 'month') {
                yearDiv.style.display = 'block';
                monthDiv.style.display = 'block';
            }
        }
        
        // Exécuter au chargement de la page
        document.addEventListener('DOMContentLoaded', function() {
            togglePeriodOptions();
        });
    </script>
</body>

</html>