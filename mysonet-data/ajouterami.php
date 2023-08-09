<?php
    // Commencer la session
    session_start();
    
    include 'db.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter un ami - MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<div class="header">
        <?php if (isset($_SESSION['pseudo'])): ?>
            <div></div>
        <?php endif; ?>
        <span><?php if (isset($_SESSION['pseudo'])) {echo $_SESSION['pseudo'].' - ';} ?>MySoNet.Online</span>
        <?php if (isset($_SESSION['pseudo'])): ?>
            <a href="logout.php" class="power-btn"><img src="power.svg" alt="Logout"></a>
        <?php endif; ?>
    </div>
    <div class="container"><h3>

<?php
    // Vérifier si l'utilisateur est connecté
    if(!isset($_SESSION['pseudo']) || !isset($_SESSION['ip'])) {
        echo "Vous devez être connecté pour ajouter un nouvel ami. <a href='rechercher.php'>Retour à la page de connexion</a>";
        echo "<script>setTimeout(function(){window.location.href = 'rechercher.php';}, 5000);</script></h3></div>";
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
        die("L'utilisateur demandé n'existe pas.<br>Vous allez être redirigé...<script>setTimeout(function(){window.location.href = 'rechercher.php';}, 5000);</script></h3></div>");
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
        die("Une demande d'ami similaire existe déjà.<br>Vous allez être redirigé...<script>setTimeout(function(){window.location.href = 'rechercher.php';}, 5000);</script></h3></div>");
    }

    //Anti-injections Commande Shell
    $secure_ami_ip = escapeshellarg($ami_ip);

    // Créer une référence pour la demande
    $ref_demande = bin2hex(random_bytes(16));

    // Vérifiez si l'IP est valide en envoyant un ping
    exec("ping -c 1 -W 2 " . $secure_ami_ip, $output, $result);

    if ($result != 0) {
        $statut = "En attente de reconnexion";
    }

    // Insérez le pseudo, l'IP, la date/heure et une référence dans le fichier demandes_en_attente
    else {
        $statut = "Réponse en attente";
        $date = new DateTime();
        $date = $date->format('Y-m-d H:i:s');  // Format de la date MySQL datetime
        $request = $ref_demande . ";" . $_SESSION['pseudo'] . ";" . $_SESSION['ip'] . ";" . $date;
        $command = 'echo "' . $request . '" >> /home/inspectorsonet/demandes_en_attente';

        // Utiliser escapeshellarg pour sécuriser l'IP de l'ami
        $secure_ami_ip = escapeshellarg($ami_ip);

        // Exécuter la commande ssh
        $ssh_command = 'sudo ssh -oStrictHostKeyChecking=no -oUserKnownHostsFile=/dev/null inspectorsonet@' . $secure_ami_ip . ' ' . escapeshellarg($command);
        shell_exec($ssh_command);

        // Lire la dernière ligne du fichier demandes_en_attente
        $last_line_command = 'sudo ssh -oStrictHostKeyChecking=no -oUserKnownHostsFile=/dev/null inspectorsonet@' . $secure_ami_ip . ' tail -n 1 /home/inspectorsonet/demandes_en_attente';
        $last_line = shell_exec($last_line_command);

        // Extraire la référence de la dernière ligne
        $last_request = explode(';', trim($last_line))[0];

        // Vérifier que la demande d'ami a été correctement enregistrée
        if ($last_request !== $ref_demande) {
            die("La demande d'ami n'a pas été correctement enregistrée. Veuillez réessayer.<br>Vous allez être redirigé...<script>setTimeout(function(){window.location.href = 'rechercher.php';}, 5000);</script></h3></div>");
        }
    }

    // Ajouter l'information à la table demandes_ami
    $stmt = $pdo->prepare("INSERT INTO demandes_ami (id_demandeur, id_demande, statut, ref_demande) VALUES (?, ?, ?, ?)");
    $stmt->execute([$demandeur_id, $demande_id, $statut, $ref_demande]);

    // Communiquer la référence au demande
    $command = 'echo "' . $ref_demande . ";" . $ami_pseudo . '" >> /home/inspectorsonet/demandes_envoyees';
    $ssh_command = 'sudo ssh -oStrictHostKeyChecking=no -oUserKnownHostsFile=/dev/null inspectorsonet@' . $_SESSION['ip'] . ' ' . escapeshellarg($command);
    shell_exec($ssh_command);
    
    echo "Demande d'ami envoyée à " . htmlspecialchars($ami_pseudo, ENT_QUOTES, 'UTF-8') . ".<br>Vous allez être redirigé...<script>setTimeout(function(){window.location.href = 'rechercher.php';}, 5000);</script></h3></div>";
?>