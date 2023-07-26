<?php
    // Commencer la session
    session_start();

    // Connexion à la base de données
    $pdo = new PDO('mysql:host=db;dbname=mysonet', 'mysonet', '123456a.');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupérer toutes les demandes en attente de reconnexion
    $stmt = $pdo->prepare("SELECT id_demandeur, id_demande FROM demandes_ami WHERE statut = 'En attente de reconnexion'");
    $stmt->execute();
    $demandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Parcourir toutes les demandes
    foreach($demandes as $demande) {
        // Récupérer l'IP du demandé
        $stmt = $pdo->prepare("SELECT ip_add FROM mysonetusers WHERE id = ?");
        $stmt->execute([$demande['id_demande']]);
        $ip = $stmt->fetchColumn();

        // Pinger l'IP avec un délai d'attente de 2 secondes
        exec("ping -c 1 -W 2 " . escapeshellarg($ip), $output, $result);

        // Si le ping est OK, connectez-vous en ssh et ajoutez les informations dans le fichier
        if($result == 0) {
            // Format de la date MySQL datetime
            $date = new DateTime();
            $date = $date->format('Y-m-d H:i:s');

            // Préparer la commande SSH
            $command = 'echo "' . $demande['id_demandeur'] . ';' . $date . '" | sudo ssh -oStrictHostKeyChecking=no -oUserKnownHostsFile=/dev/null inspectorsonet@' . escapeshellarg($ip) . ' "cat >> /home/inspectorsonet/demandes_en_attente"';

            // Exécuter la commande SSH
            shell_exec($command);

            // Lire la dernière ligne du fichier demandes_en_attente
            $last_line_command = 'sudo ssh -oStrictHostKeyChecking=no -oUserKnownHostsFile=/dev/null inspectorsonet@' . escapeshellarg($ip) . ' "tail -n 1 /home/inspectorsonet/demandes_en_attente"';
            $last_line = shell_exec($last_line_command);

            // Extraire l'id de la dernière demande
            $last_request_id = explode(';', $last_line)[0];

            // Si les données sont correctement inscrites, mettre à jour le statut dans la base de données
            if ($last_request_id == $demande['id_demandeur']) {
                $stmt = $pdo->prepare("UPDATE demandes_ami SET statut = 'Réponse en attente' WHERE id_demandeur = ? AND id_demande = ?");
                $stmt->execute([$demande['id_demandeur'], $demande['id_demande']]);
            }
        }
    }
?>
