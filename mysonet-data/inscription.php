<?php
$host = 'db';  // nom du service MySQL dans le fichier docker-compose.yml
$db   = 'mysonet';
$user = 'mysonet';
$pass = '123456a.';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];
$pdo = new PDO($dsn, $user, $pass, $opt);

$pseudo = $_POST['pseudo'];
$ip = $_POST['ip'];
$idmysonet = $_POST['idmysonet'];

// Vérifiez si le pseudo est unique et valide
if (!preg_match('/^[a-zA-Z0-9_-]{2,50}$/', $pseudo)) {
    die("Le pseudo contient des caractères non autorisés ou n'a pas la longueur requise. Exemple : MonPseudo_1234");
}

$stmt = $pdo->prepare("SELECT * FROM mysonetusers WHERE username = ?");
$stmt->execute([$pseudo]);

if ($stmt->rowCount() > 0) {
    die("Le pseudo existe déjà. Choisissez-en un autre. S''il vous appartient et que vous avez une nouvelle IP, vous ne pourrez pas le supprimer de la liste vous-même. Pour ce faire, inscrivez-vous avec un nouveau pseudo et faites une demande d'ami à votre ancien pseudo. Dans 15 jours, celui-ci sera supprimé. Vous devrez vous réinscrire avec votre ancien pseudo.");
}

// Vérifiez si l'IP est unique et valide
if (!filter_var($ip, FILTER_VALIDATE_IP)) {
    die("L'adresse IP n'est pas valide.");
}

$stmt = $pdo->prepare("SELECT * FROM mysonetusers WHERE ip_add = ?");
$stmt->execute([$ip]);

if ($stmt->rowCount() > 0) {
    die("L'IP existe déjà dans la liste. Vous vous êtes déjà inscrit. Si vous voulez vous inscrire avec un nouveau pseudo, supprimez-le de la liste et refaites une nouvelle inscription.");
}

//Anti-injections Commande Shell
$secure_ip = escapeshellarg($ip);

// Vérifiez si l'IP est valide en envoyant un ping
exec("ping -c 1 -W 2 " . $secure_ip, $output, $result);

if ($result != 0) {
    die("L'IP n'est pas valide ou n'est pas accessible.");
}

// SSH vers le serveur distant et vérifier l'ID MySonet
if (!preg_match('/^[a-zA-Z0-9_-]{2,50}$/', $idmysonet)) {
    die("L'ID MySonet contient des caractères non autorisés ou n'a pas la longueur requise. Exemple : MonID_1234");
}

$file_content=shell_exec("sudo ssh -oStrictHostKeyChecking=no -oUserKnownHostsFile=/dev/null inspectorsonet@" . $secure_ip . " 'cat /home/inspectorsonet/idmysonet' | tail -n 1");

if (trim($file_content) != $idmysonet) {
    die("L'ID MySonet ne correspond pas à celui de votre serveur.");
}

// Insérez le nouvel utilisateur dans la base de données
$stmt = $pdo->prepare("INSERT INTO mysonetusers (username, ip_add) VALUES (?, ?)");
$stmt->execute([$pseudo, $ip]);

echo "Inscription réussie !";
?>
