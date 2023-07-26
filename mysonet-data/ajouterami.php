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
    exec("ping -c 1 -W 2 " . $secure_ami_ip, $output, $result);

    if ($result != 0) {
        $statut = "En attente de reconnexion";
    }

    // Insérez le pseudo, l'IP et la date/heure dans le fichier demandes_en_attente
    else {
        $statut = "Réponse en attente";
        $date = new DateTime();
        $date = $date->format('Y-m-d H:i:s');  // Format de la date MySQL datetime
        $request = $_SESSION['pseudo'] . ";" . $_SESSION['ip'] . ";" . $date;
        $command = 'echo "' . $request . '" >> /home/inspectorsonet/demandes_en_attente';

        // Utiliser escapeshellarg pour sécuriser l'IP de l'ami
        $secure_ami_ip = escapeshellarg($ami_ip);

        // Exécuter la commande ssh
        $ssh_command = 'sudo ssh -oStrictHostKeyChecking=no -oUserKnownHostsFile=/dev/null inspectorsonet@' . $secure_ami>
        shell_exec($ssh_command);

        // Lire la dernière ligne du fichier demandes_en_attente
        $last_line_command = 'sudo ssh -oStrictHostKeyChecking=no -oUserKnownHostsFile=/dev/null inspectorsonet@' . $secure_ami_ip . ' tail -n 1 /home/inspectorsonet/demandes_en_attente';
        $last_line = shell_exec($last_line_command);

        // Extraire le pseudo de la dernière ligne
        $last_request_pseudo = explode(';', $last_line)[0];

        // Vérifier que la demande d'ami a été correctement enregistrée
        if ($last_request_pseudo !== $_SESSION['pseudo']) {
            die("La demande d'ami n'a pas été correctement enregistrée. Veuillez réessayer.");
        }
    }

    // Récupérer les ID des utilisateurs en fonction de leurs pseudos
    $stmt = $pdo->prepare("SELECT id FROM mysonetusers WHERE username = ?");
    $stmt->execute([$_SESSION['pseudo']]);
    $demandeur_id = $stmt->fetchColumn();
    $stmt->execute([$ami_pseudo]);
    $demande_id = $stmt->fetchColumn();

    // Vérifier si une demande d'ami similaire existe déjà
    $stmt = $pdo->prepare("SELECT * FROM demandes_ami WHERE id_demandeur = ? AND id_demande = ?");
    $stmt->execute([$demandeur_id, $demande_id]);
    $demande_existante = $stmt->fetch();

    if ($demande_existante) {
        die("Une demande d'ami similaire existe déjà.");
    }

    // Ajouter l'information à la table demandes_ami
    $stmt = $pdo->prepare("INSERT INTO demandes_ami (id_demandeur, id_demande, statut) VALUES (?, ?, ?)");
    $stmt->execute([$demandeur_id, $demande_id, $statut]);

    echo "Demande d'ami envoyée à " . htmlspecialchars($ami_pseudo, ENT_QUOTES, 'UTF-8') . ".";
?>
