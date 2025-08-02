<?php
require_once '../config.php';

$success = $error = '';
$envoi = null;

// Si un ID d'envoi est fourni, pré-remplir le formulaire
if (isset($_GET['id'])) {
    $idenvoi = (int)$_GET['id'];
    
    // Récupérer les informations de l'envoi
    $sql = "SELECT e.*, v.design, i.villedep, i.villearr 
            FROM envoyer e
            JOIN voiture v ON e.idvoit = v.idvoit
            JOIN itineraire i ON v.codeit = i.codeit
            WHERE e.idenvoi = $idenvoi";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $envoi = $result->fetch_assoc();
        
        // Vérifier si l'envoi a déjà été reçu
        $check_reception = "SELECT idrecept FROM recevoir WHERE idenvoi = $idenvoi";
        $result_reception = $conn->query($check_reception);
        
        if ($result_reception->num_rows > 0) {
            $error = "Ce colis a déjà été marqué comme reçu.";
            $envoi = null; // Réinitialiser pour ne pas afficher le formulaire
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idenvoi = (int)$_POST['idenvoi'];
    $date_recept = $conn->real_escape_string($_POST['date_recept']);
    
    // Formater la date
    $date_recept_formatted = date('Y-m-d H:i:s', strtotime($date_recept));
    
    // Vérifier si l'envoi existe et n'est pas déjà reçu
    $check_sql = "SELECT e.idenvoi, e.emailEnvoyeur, e.nomEnvoyeur, e.colis 
                  FROM envoyer e
                  LEFT JOIN recevoir r ON e.idenvoi = r.idenvoi
                  WHERE e.idenvoi = $idenvoi AND r.idrecept IS NULL";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows === 0) {
        $error = "L'envoi n'existe pas ou a déjà été reçu.";
    } else {
        // Récupérer les informations de l'envoi pour l'email
        $envoi_info = $check_result->fetch_assoc();
        
        // Insérer la réception
        $sql = "INSERT INTO recevoir (idenvoi, date_recept) VALUES ($idenvoi, '$date_recept_formatted')";
        
        if ($conn->query($sql) === TRUE) {
            $last_id = $conn->insert_id;
            $success = "Réception enregistrée avec succès.";
            
            // Simuler l'envoi d'un email à l'expéditeur
            // Dans un environnement réel, vous utiliseriez la fonction mail() ou une bibliothèque comme PHPMailer
            $email_envoyeur = $envoi_info['emailEnvoyeur'];
            $nom_envoyeur = $envoi_info['nomEnvoyeur'];
            $description_colis = $envoi_info['colis'];
            
            $success .= "<div class='mt-3 p-3 border rounded bg-light'>
                            <h6>Email de notification envoyé à: $email_envoyeur</h6>
                            <p><strong>Sujet:</strong> Votre colis a été réceptionné</p>
                            <p><strong>Message:</strong><br>
                            Bonjour $nom_envoyeur,<br><br>
                            Nous vous informons que votre colis \"$description_colis\" a été réceptionné le " . date('d/m/Y à H:i', strtotime($date_recept_formatted)) . ".<br><br>
                            Merci d'avoir utilisé nos services.<br><br>
                            Cordialement,<br>
                            Coopérative de Transport
                            </p>
                        </div>";
        } else {
            $error = "Erreur: " . $sql . "<br>" . $conn->error;
        }
    }
}

// Récupérer la liste des envois pour le formulaire s'il n'y a pas d'ID spécifique
if (!isset($_GET['id']) || $error) {
    $sql_envois = "SELECT e.idenvoi, e.colis, e.date_envoi, e.nomEnvoyeur, e.nomRecepteur, v.design, i.villedep, i.villearr
                  FROM envoyer e
                  JOIN voiture v ON e.idvoit = v.idvoit
                  JOIN itineraire i ON v.codeit = i.codeit
                  LEFT JOIN recevoir r ON e.idenvoi = r.idenvoi
                  WHERE r.idrecept IS NULL
                  ORDER BY e.date_envoi DESC";
    $result_envois = $conn->query($sql_envois);
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enregistrer une Réception - Gestion des colis</title>
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
                            <li><a class="dropdown-item active" href="ajouter.php">Ajouter</a></li>
                            <li><a class="dropdown-item" href="liste.php">Liste</a></li>
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
                    <h2 class="mb-4"><i class="fas fa-inbox me-2"></i>Enregistrer une réception de colis</h2>
                    
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
                    
                    <?php if ($envoi): ?>
                        <!-- Formulaire pré-rempli pour un envoi spécifique -->
                        <div class="alert alert-info">
                            <h5>Détails de l'envoi</h5>
                            <p><strong>ID:</strong> <?php echo $envoi['idenvoi']; ?></p>
                            <p><strong>Colis:</strong> <?php echo htmlspecialchars($envoi['colis']); ?></p>
                            <p><strong>Envoyé le:</strong> <?php echo date('d/m/Y H:i', strtotime($envoi['date_envoi'])); ?></p>
                            <p><strong>Expéditeur:</strong> <?php echo htmlspecialchars($envoi['nomEnvoyeur']); ?></p>
                            <p><strong>Destinataire:</strong> <?php echo htmlspecialchars($envoi['nomRecepteur']); ?> (<?php echo htmlspecialchars($envoi['contactRecepteur']); ?>)</p>
                            <p><strong>Voiture:</strong> <?php echo htmlspecialchars($envoi['idvoit'] . ' - ' . $envoi['design']); ?></p>
                            <p><strong>Itinéraire:</strong> <?php echo htmlspecialchars($envoi['villedep'] . ' - ' . $envoi['villearr']); ?></p>
                        </div>
                        
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" novalidate>
                            <input type="hidden" name="idenvoi" value="<?php echo $envoi['idenvoi']; ?>">
                            <div class="mb-3">
                                <label for="date_recept" class="form-label">Date de réception</label>
                                <input type="datetime-local" class="form-control" id="date_recept" name="date_recept" required>
                                <div class="invalid-feedback">
                                    Veuillez saisir la date de réception.
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer la réception</button>
                                <a href="liste.php" class="btn btn-secondary"><i class="fas fa-list me-2"></i>Liste des réceptions</a>
                            </div>
                        </form>
                    <?php else: ?>
                        <!-- Formulaire standard de sélection d'un envoi -->
                        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="idenvoi" class="form-label">Sélectionner un colis à réceptionner</label>
                                <select class="form-select" id="idenvoi" name="idenvoi" required>
                                    <option value="">Choisir un envoi...</option>
                                    <?php
                                    if (isset($result_envois) && $result_envois->num_rows > 0) {
                                        while($row = $result_envois->fetch_assoc()) {
                                            echo "<option value='" . $row['idenvoi'] . "'>" 
                                                . "ID #" . $row['idenvoi'] . " - " 
                                                . htmlspecialchars($row['colis']) . " - "
                                                . "De: " . htmlspecialchars($row['nomEnvoyeur']) . " - "
                                                . "Pour: " . htmlspecialchars($row['nomRecepteur']) . " - "
                                                . "Envoyé le: " . date('d/m/Y', strtotime($row['date_envoi'])) 
                                                . "</option>";
                                        }
                                    }
                                    ?>
                                </select>
                                <div class="invalid-feedback">
                                    Veuillez sélectionner un envoi à réceptionner.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="date_recept" class="form-label">Date de réception</label>
                                <input type="datetime-local" class="form-control" id="date_recept" name="date_recept" required>
                                <div class="invalid-feedback">
                                    Veuillez saisir la date de réception.
                                </div>
                            </div>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer la réception</button>
                                <a href="liste.php" class="btn btn-secondary"><i class="fas fa-list me-2"></i>Liste des réceptions</a>
                            </div>
                        </form>
                    <?php endif; ?>
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
            // Set default date to current datetime
            const dateReceptInput = document.getElementById('date_recept');
            const now = new Date();
            const year = now.getFullYear();
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const day = String(now.getDate()).padStart(2, '0');
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            
            dateReceptInput.value = `${year}-${month}-${day}T${hours}:${minutes}`;
        });
    </script>
</body>

</html>