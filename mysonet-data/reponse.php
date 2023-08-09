<?php
if (isset($_GET['ref_demande'])) {
    $ref_demande = $_GET['ref_demande'];

    try {
        include 'db.php';
        
        // Supprimer la demande d'ami
        $stmt = $pdo->prepare("DELETE FROM demandes_ami WHERE ref_demande = :ref_demande");
        $stmt->bindParam(':ref_demande', $ref_demande);
        $stmt->execute();

        if (isset($_GET['ip_add']) && isset($_GET['token'])) {
            $ip_add = $_GET['ip_add'];
            $token = $_GET['token'];
            header('Location: http://'.$ip_add.'/accepte2.php?ref_demande='.$ref_demande.'&token='.$token);
        } if (isset($_GET['ip_add'])) {
            $ip_add = $_GET['ip_add'];
            header('Location: http://'.$ip_add.'/notif.php');
        }
        
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}
?>
