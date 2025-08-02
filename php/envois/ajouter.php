<?php
require_once '../config.php';

$success = $error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idvoit = $conn->real_escape_string($_POST['idvoit']);
    $colis = $conn->real_escape_string($_POST['colis']);
    $nomEnvoyeur = $conn->real_escape_string($_POST['nomEnvoyeur']);
    $emailEnvoyeur = $conn->real_escape_string($_POST['emailEnvoyeur']);
    $date_envoi = $conn->real_escape_string($_POST['date_envoi']);
    $frais = (int)$_POST['frais'];
    $nomRecepteur = $conn->real_escape_string($_POST['nomRecepteur']);
    $contactRecepteur = $conn->real_escape_string($_POST['contactRecepteur']);
    
    // Formater la date
    $date_envoi_formatted = date('Y-m-d H:i:s', strtotime($date_envoi));
    
    // Insérer l'envoi
    $sql = "INSERT INTO envoyer (idvoit, colis, nomEnvoyeur, emailEnvoyeur, date_envoi, frais, nomRecepteur, contactRecepteur) 
            VALUES ('$idvoit', '$colis', '$nomEnvoyeur', '$emailEnvoyeur', '$date_envoi_formatted', $frais, '$nomRecepteur', '$contactRecepteur')";
    
    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        $success = "Envoi enregistré avec succès. <a href='recu.php?id=$last_id' target='_blank' class='btn btn-sm btn-outline-success ms-2'><i class='fas fa-file-pdf me-1'></i>Imprimer le reçu</a>";
    } else {
        $error = "Erreur: " . $sql . "<br>" . $conn->error;
    }
}

// Récupérer toutes les voitures pour le menu déroulant
$sql_voitures = "SELECT v.idvoit, v.design, i.villedep, i.villearr, i.frais 
                FROM voiture v 
                JOIN itineraire i ON v.codeit = i.codeit 
                ORDER BY v.idvoit";
$result_voitures = $conn->query($sql_voitures);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Envoi - Gestion des colis</title>
    
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
                            <li><a class="dropdown-item active" href="ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item" href="liste.php">Liste</a></li>
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
            <div class="col-md-10 offset-md-1">
                <div class="form-container">
                    <h2 class="mb-4"><i class="fas fa-paper-plane me-2"></i>Ajouter un envoi de colis</h2>
                    
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
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3 text-primary">Informations du colis</h5>
                                <div class="mb-3">
                                    <label for="idvoit" class="form-label">Voiture</label>
                                    <select class="form-select" id="idvoit" name="idvoit" required>
                                        <option value="">Sélectionner une voiture</option>
                                        <?php
                                        if ($result_voitures->num_rows > 0) {
                                            while($row = $result_voitures->fetch_assoc()) {
                                                echo "<option value='" . $row['idvoit'] . "' data-frais='" . $row['frais'] . "'>" 
                                                    . $row['idvoit'] . " - " . $row['design'] . " (" . $row['villedep'] . " à " . $row['villearr'] . ")</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        Veuillez sélectionner une voiture.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="colis" class="form-label">Description du colis</label>
                                    <textarea class="form-control" id="colis" name="colis" rows="2" required></textarea>
                                    <div class="invalid-feedback">
                                        Veuillez décrire le colis.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="date_envoi" class="form-label">Date d'envoi</label>
                                    <input type="datetime-local" class="form-control" id="date_envoi" name="date_envoi" required>
                                    <div class="invalid-feedback">
                                        Veuillez saisir la date d'envoi.
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="frais" class="form-label">Frais</label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="frais" name="frais" min="0" required>
                                        <span class="input-group-text">Ar</span>
                                    </div>
                                    <div class="invalid-feedback">
                                        Veuillez saisir le montant des frais.
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h5 class="mb-3 text-primary">Informations de l'expéditeur</h5>
                                        <div class="mb-3">
                                            <label for="nomEnvoyeur" class="form-label">Nom de l'expéditeur</label>
                                            <input type="text" class="form-control" id="nomEnvoyeur" name="nomEnvoyeur" required>
                                            <div class="invalid-feedback">
                                                Veuillez saisir le nom de l'expéditeur.
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="emailEnvoyeur" class="form-label">Email de l'expéditeur</label>
                                            <input type="email" class="form-control" id="emailEnvoyeur" name="emailEnvoyeur" required>
                                            <div class="invalid-feedback">
                                                Veuillez saisir une adresse email valide.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <h5 class="mb-3 text-primary">Informations du destinataire</h5>
                                        <div class="mb-3">
                                            <label for="nomRecepteur" class="form-label">Nom du destinataire</label>
                                            <input type="text" class="form-control" id="nomRecepteur" name="nomRecepteur" required>
                                            <div class="invalid-feedback">
                                                Veuillez saisir le nom du destinataire.
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="contactRecepteur" class="form-label">Contact du destinataire</label>
                                            <input type="text" class="form-control" id="contactRecepteur" name="contactRecepteur" required>
                                            <div class="invalid-feedback">
                                                Veuillez saisir le contact du destinataire.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer l'envoi</button>
                            <a href="liste.php" class="btn btn-secondary"><i class="fas fa-list me-2"></i>Liste des envois</a>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Automatically fill in the frais field based on selected vehicle
            const idvoitSelect = document.getElementById('idvoit');
            const fraisInput = document.getElementById('frais');
            
            idvoitSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                if (selectedOption.value) {
                    fraisInput.value = selectedOption.dataset.frais;
                } else {
                    fraisInput.value = '';
                }
            });
            
            // Set default date to current datetime
            const dateEnvoiInput = document.getElementById('date_envoi');
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            dateEnvoiInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        });
    </script>
</body>

</html>