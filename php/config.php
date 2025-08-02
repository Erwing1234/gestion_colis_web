<?php
// Configuration de la base de données
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'cooperative_db');

// Connexion à la base de données
$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD);

// Vérifier la connexion
if($conn->connect_error) {
    die("Erreur de connexion: " . $conn->connect_error);
}

// Création de la base de données si elle n'existe pas
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if($conn->query($sql) !== TRUE) {
    die("Erreur lors de la création de la base de données: " . $conn->error);
}

// Sélectionner la base de données
$conn->select_db(DB_NAME);

// Création des tables
// Table Itineraire
$sql = "CREATE TABLE IF NOT EXISTS itineraire (
    codeit VARCHAR(20) PRIMARY KEY,
    villedep VARCHAR(100) NOT NULL,
    villearr VARCHAR(100) NOT NULL,
    frais INT NOT NULL
)";
if($conn->query($sql) !== TRUE) {
    die("Erreur lors de la création de la table itineraire: " . $conn->error);
}

// Table VOITURE
$sql = "CREATE TABLE IF NOT EXISTS voiture (
    idvoit VARCHAR(20) PRIMARY KEY,
    design VARCHAR(100) NOT NULL,
    codeit VARCHAR(20) NOT NULL,
    FOREIGN KEY (codeit) REFERENCES itineraire(codeit) ON DELETE CASCADE
)";
if($conn->query($sql) !== TRUE) {
    die("Erreur lors de la création de la table voiture: " . $conn->error);
}

// Table ENVOYER
$sql = "CREATE TABLE IF NOT EXISTS envoyer (
    idenvoi INT AUTO_INCREMENT PRIMARY KEY,
    idvoit VARCHAR(20) NOT NULL,
    colis VARCHAR(100) NOT NULL,
    nomEnvoyeur VARCHAR(100) NOT NULL,
    emailEnvoyeur VARCHAR(100) NOT NULL,
    date_envoi DATETIME NOT NULL,
    frais INT NOT NULL,
    nomRecepteur VARCHAR(100) NOT NULL,
    contactRecepteur VARCHAR(50) NOT NULL,
    FOREIGN KEY (idvoit) REFERENCES voiture(idvoit) ON DELETE CASCADE
)";
if($conn->query($sql) !== TRUE) {
    die("Erreur lors de la création de la table envoyer: " . $conn->error);
}

// Table RECEVOIR
$sql = "CREATE TABLE IF NOT EXISTS recevoir (
    idrecept INT AUTO_INCREMENT PRIMARY KEY,
    idenvoi INT NOT NULL,
    date_recept DATETIME NOT NULL,
    FOREIGN KEY (idenvoi) REFERENCES envoyer(idenvoi) ON DELETE CASCADE
)";
if($conn->query($sql) !== TRUE) {
    die("Erreur lors de la création de la table recevoir: " . $conn->error);
}

// Configuration pour l'envoi d'e-mails
define('MAIL_HOST', 'smtp.example.com');
define('MAIL_USERNAME', 'notification@cooperative.com');
define('MAIL_PASSWORD', 'password');
define('MAIL_FROM', 'notification@cooperative.com');
define('MAIL_FROM_NAME', 'Coopérative de Transport');

// Format de date
define('DATE_FORMAT', 'Y-m-d H:i:s');

// Chemin d'accès aux fichiers
define('ROOT_PATH', dirname(dirname(__FILE__)));

// Fonction pour générer un numéro de reçu
function generateReceiptNumber() {
    return 'RECU-' . rand(100, 999);
}

// Fonction pour afficher les messages d'alerte
function showAlert($message, $type = 'success') {
    echo "<div class='alert alert-{$type} alert-dismissible fade show' role='alert'>
            {$message}
            <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
          </div>";
}
?>