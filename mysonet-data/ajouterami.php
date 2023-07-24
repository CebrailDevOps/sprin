<?php
    // Commencer la session
    session_start();

    // Connexion à la base de données
    $pdo = new PDO('mysql:host=db;dbname=mysonet', 'mysonet', '123456a.');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifier si l'utilisateur est connecté
    if(!isset($_SESSION['pseudo']) || !isset($_SESSION['ip'])) {
        echo "Vous devez être connecté pour ajouter un nouvel ami. <a href='connexion.php'>Retour à la page de connexion</a>";
        echo "<script>setTimeout(function(){window.location.href = 'connexion.php';}, 5000);</script>";
        exit();
    }

    // Récupérer le pseudo de l'ami
    $ami_pseudo = $_POST['ami_pseudo'];

    // Obtenir l'IP de l'ami à partir de la base de données
    $stmt = $pdo->prepare("SELECT ip_add FROM mysonetusers WHERE username = ?");
    $stmt->execute([$ami_pseudo]);
    $ami_ip = $stmt->fetchColumn();

    // Si l'ami n'existe pas dans la base de données
    if ($ami_ip === false) {
        die("L'utilisateur demandé n'existe pas.");
    }

    //Anti-injections Commande Shell
    $secure_ami_ip = escapeshellarg($ami_ip);

    // Vérifiez si l'IP est valide en envoyant un ping
    exec("ping -c 1 " . $secure_ami_ip, $output, $result);

    if ($result != 0) {
        die("L'IP de l'ami n'est pas valide ou n'est pas accessible.");
    }

    // Insérez le pseudo, l'IP et la date/heure dans le fichier demandes_en_attente
    $date = new DateTime();
    $date = $date->format('Y-m-d H:i:s');  // Format de la date MySQL datetime
    $request = $_SESSION['pseudo'] . ";" . $_SESSION['ip'] . ";" . $date;
    $command = 'echo "' . $request . '" >> /home/inspectorsonet/demandes_en_attente';

    // Utiliser escapeshellarg pour sécuriser l'IP de l'ami
    $secure_ami_ip = escapeshellarg($ami_ip);

    // Exécuter la commande ssh
    $ssh_command = 'sudo ssh -oStrictHostKeyChecking=no -oUserKnownHostsFile=/dev/null inspectorsonet@' . $secure_ami>
    shell_exec($ssh_command);

    echo "Demande d'ami envoyée à " . htmlspecialchars($ami_pseudo, ENT_QUOTES, 'UTF-8') . ".";
?>

