<?php
    // Commencer la session
    session_start();

    // Vérifier si l'utilisateur est connecté
    if(!isset($_SESSION['pseudo']) || !isset($_SESSION['ip'])) {
        echo "Vous devez être connecté pour rechercher de nouveaux amis. <a href='rechercher.php'>Retour à la page de connexion</a>";
        echo "<script>setTimeout(function(){window.location.href = 'rechercher.php';}, 5000);</script>";
        exit();
    }

    // Vérifier le formulaire
    $pseudo = $_POST['pseudo'];
    if (!isset($pseudo)) {
        echo "Aucun pseudo fourni. <a href='rechercher.php'>Retour à la page de connexion</a>";
        echo "<script>setTimeout(function(){window.location.href = 'rechercher.php';}, 5000);</script>";
        exit();
    }

    if (!preg_match('/^[a-zA-Z0-9_-]{2,50}$/', $pseudo)) {
        die("Le pseudo contient des caractères non autorisés ou n'a pas la longueur requise. Exemple : MonPseudo_1234");
    }

    // Connexion à la base de données
    $dbh = new PDO('mysql:host=db;dbname=mysonet', 'mysonet', '123456a.');

    // Requête pour rechercher des utilisateurs
    $stmt = $dbh->prepare("SELECT username FROM mysonetusers WHERE username LIKE :pseudo");
    $pseudoLike = "%" . $pseudo . "%";
    $stmt->bindParam(':pseudo', $pseudoLike, PDO::PARAM_STR);
    $stmt->execute();

    // Afficher les résultats
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') . " 
        <form method='post' action='ajouterami.php'>
            <input type='hidden' name='ami_pseudo' value='". htmlspecialchars($row['username'], ENT_QUOTES, 'UTF-8') ."'>
            <button type='submit'>Demander en ami</button>
        </form><br>";
    }
    if ($stmt->rowCount() == 0) {
        echo "Aucun utilisateur trouvé avec le pseudo '$pseudo'.";
    }
    echo "<a href='rechercher.php'>Retour à la page de recherche</a>";
?>
