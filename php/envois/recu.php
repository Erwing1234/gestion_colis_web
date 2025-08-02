<?php
require_once '../config.php';

// Vérifier si un ID est fourni
if (!isset($_GET['id'])) {
    die("ID d'envoi non fourni.");
}

$id = (int)$_GET['id'];

// Récupérer les informations de l'envoi
$sql = "SELECT e.*, v.design, i.villedep, i.villearr 
        FROM envoyer e
        JOIN voiture v ON e.idvoit = v.idvoit
        JOIN itineraire i ON v.codeit = i.codeit
        WHERE e.idenvoi = $id";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    die("Envoi non trouvé.");
}

$envoi = $result->fetch_assoc();

// Générer un numéro de reçu
$num_recu = "RECU-" . str_pad($id, 3, "0", STR_PAD_LEFT);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reçu d'envoi - Gestion des colis</title>
    <link rel="icon" href="https://public-frontend-cos.metadl.com/mgx/img/favicon.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../css/style.css">
    <style>
        @media print {
            body {
                font-size: 12pt;
            }
            .no-print {
                display: none !important;
            }
            .receipt {
                border: none !important;
                box-shadow: none !important;
            }
            .container {
                width: 100%;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5">
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="receipt">
                    <div class="receipt-header">
                        <h2>Coopérative de Transport</h2>
                        <h3 class="text-primary"><?php echo $num_recu; ?></h3>
                    </div>
                    
                    <div class="receipt-body">
                        <div class="row mb-4">
                            <div class="col-6">
                                <p><strong>Date d'envoi:</strong><br>
                                <?php echo date('d/m/Y H:i', strtotime($envoi['date_envoi'])); ?></p>
                            </div>
                            <div class="col-6 text-end">
                                <p><strong>Destination:</strong><br>
                                <?php echo htmlspecialchars($envoi['villedep']) . ' - ' . htmlspecialchars($envoi['villearr']); ?></p>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-6">
                                <h5>Expéditeur</h5>
                                <p><?php echo htmlspecialchars($envoi['nomEnvoyeur']); ?><br>
                                Email: <?php echo htmlspecialchars($envoi['emailEnvoyeur']); ?></p>
                            </div>
                            <div class="col-6">
                                <h5>Destinataire</h5>
                                <p><?php echo htmlspecialchars($envoi['nomRecepteur']); ?><br>
                                Contact: <?php echo htmlspecialchars($envoi['contactRecepteur']); ?></p>
                            </div>
                        </div>
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5>Détails du colis</h5>
                                <table class="table table-bordered">
                                    <tr>
                                        <td><strong>Voiture:</strong></td>
                                        <td><?php echo htmlspecialchars($envoi['idvoit'] . ' - ' . $envoi['design']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Description:</strong></td>
                                        <td><?php echo htmlspecialchars($envoi['colis']); ?></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Frais:</strong></td>
                                        <td><?php echo number_format($envoi['frais'], 0, ',', ' ') . ' Ar'; ?></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        
                        <div class="row mt-5">
                            <div class="col-6">
                                <div>
                                    <p>Signature de l'agent</p>
                                    <div style="border-bottom: 1px solid #ccc; height: 40px;"></div>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <div>
                                    <p>Signature du client</p>
                                    <div style="border-bottom: 1px solid #ccc; height: 40px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="receipt-footer mt-5">
                        <p class="mb-0"><small>Ce reçu est généré électroniquement et est valable sans signature.</small></p>
                    </div>
                </div>
                
                <div class="d-flex justify-content-center mt-4 no-print">
                    <button class="btn btn-primary me-2 btn-print"><i class="fas fa-print me-2"></i>Imprimer</button>
                    <a href="liste.php" class="btn btn-secondary"><i class="fas fa-arrow-left me-2"></i>Retour</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/script.js"></script>
</body>

</html>