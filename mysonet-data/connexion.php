<?php
    session_start();

    // Rediriger vers recherche.php si l'utilisateur est déjà connecté
    if(isset($_SESSION['pseudo']) && isset($_SESSION['ip'])) {
        header('Location: rechercher.php');
        exit();
    }

    // Récupération des valeurs du formulaire
    $pseudo = $_POST['pseudo'];
    $ip = $_POST['ip'];
    $idmysonet = $_POST['idmysonet'];

?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion - MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header"><?php if (isset($_SESSION['pseudo'])) {echo $_SESSION['pseudo'].' - ';} ?>MySoNet.Online</div>
    <div class="container"><h3>
<?php
    // Validation des entrées
    if (!preg_match('/^[a-zA-Z0-9_-]{2,50}$/', $pseudo)) {
        die("Le pseudo contient des caractères non autorisés ou n'a pas la longueur requise.<br>Exemple : MonPseudo_1234<script>setTimeout(function(){window.location.href = 'rechercher.php';}, 5000);</script><br>Vous allez être redirigé...</h3></div></body></html>");
    }

    if (!filter_var($ip, FILTER_VALIDATE_IP)) {
        die("L'adresse IP n'est pas valide.<script>setTimeout(function(){window.location.href = 'rechercher.php';}, 3000);</script><br>Vous allez être redirigé...</h3></div></body></html>");
    }

    if (!preg_match('/^[a-zA-Z0-9_-]{2,50}$/', $idmysonet)) {
        die("L'ID MySonet contient des caractères non autorisés ou n'a pas la longueur requise.<br>Exemple : MonID_1234<script>setTimeout(function(){window.location.href = 'rechercher.php';}, 5000);</script><br>Vous allez être redirigé...</h3></div></body></html>");
    }

    include 'db.php';
    
    // Requête pour vérifier si l'utilisateur existe
    $stmt = $pdo->prepare("SELECT * FROM mysonetusers WHERE username = :pseudo AND ip_add = :ip");
    $stmt->bindParam(':pseudo', $pseudo);
    $stmt->bindParam(':ip', $ip);
    $stmt->execute();

    // Vérification si l'utilisateur existe
    if($stmt->rowCount() > 0) {
        //Anti-injections Commande Shell
        $secure_ip = escapeshellarg($ip);
        // l'utilisateur existe, vérification de l'IP
        exec("ping -c 1 -W 2 " . $secure_ip, $output, $result);
        if($result == 0) {
            // vérification plus appronfondie
            $file_content=shell_exec("sudo ssh -oStrictHostKeyChecking=no -oUserKnownHostsFile=/dev/null inspectorsonet@" . $secure_ip . " 'cat /home/inspectorsonet/idmysonet' | tail -n 1");
            if (trim($file_content) == $idmysonet) {
                // l'utilisateur est connecté
                echo "Vous êtes maintenant connecté.";
                // stocker l'information de connexion dans la session
                $_SESSION['pseudo'] = $pseudo;
                $_SESSION['ip'] = $ip;
            } else { echo "L'ID MySonet ne correspond pas à l'ID MySonet de votre serveur.<br>Vous allez être redirigé..."; }
        } else {
            // l'IP n'est pas valide
            echo "L'IP n'est pas valide.<br>Vous allez être redirigé...";
        }
    } else {
        // l'utilisateur n'existe pas
        echo "Pseudo ou IP invalide.<br>Vous allez être redirigé...";
    }
    echo "<script>setTimeout(function(){window.location.href = 'rechercher.php';}, 5000);</script>";
?>
    </h3></div></body></html>
<?php
    exit();
?>