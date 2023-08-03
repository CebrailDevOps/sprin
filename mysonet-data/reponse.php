<?php
if (isset($_GET['ref_demande'])) {
    $ref_demande = $_GET['ref_demande'];

    try {
        $conn = new PDO('mysql:host=db;dbname=mysonet', 'mysonet', '123456a.');
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Supprimer la demande d'ami
        $stmt = $conn->prepare("DELETE FROM demandes_ami WHERE ref_demande = :ref_demande");
        $stmt->bindParam(':ref_demande', $ref_demande);
        $stmt->execute();

        if (isset($_GET['ip_add'])) {
            $ip_add = $_GET['ip_add'];
            header('Location: http://'.$ip_add.'/accepte2.php?'.$ref_demande);
        }
    } catch(PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }
}
?>
