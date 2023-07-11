<?php
    // Commencer la session
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Connexion et Recherche</title>
</head>
<body>
<?php
    // Si l'utilisateur n'est pas connecté, afficher le formulaire de connexion
    if(!isset($_SESSION['pseudo']) || !isset($_SESSION['ip'])) {
?>

    <h2>Connexion</h2>
    <h3>Veuillez vous connecter avant de faire une recherche</h3>
    <form action="connexion.php" method="post">
        Pseudo : <input type="text" name="pseudo" required><br>
        IP de votre serveur : <input type="text" name="ip" required><br>
        ID MySonet : <input type="text" name="idmysonet" required><br>
        <input type="submit" value="Se connecter">
    </form>
<?php
    } else {
?>
    <h2>Recherche d'ami</h2>
    <form action="recherche.php" method="post">
        Pseudo : <input type="text" name="pseudo" required><br>
        <input type="submit" value="Rechercher">
    </form>
<?php
    }
?>
</body>
</html>
