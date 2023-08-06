<?php
    session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Connexion et Recherche - MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header"><?php if (isset($_SESSION['pseudo'])) {echo $_SESSION['pseudo'].' - ';} ?>MySoNet.Online</div>
    <div class="container">
        <?php
            // Si l'utilisateur n'est pas connecté, afficher le formulaire de connexion
            if(!isset($_SESSION['pseudo']) || !isset($_SESSION['ip'])) {
        ?>
        <h2>Connexion</h2>
        <h3>Veuillez vous connecter avant de faire une recherche</h3>
        <form action="connexion.php" method="post">
            <label for="pseudo">Pseudo :</label>
            <input type="text" id="pseudo" name="pseudo" required><br>
            <label for="ip">IP de votre serveur :</label>
            <input type="text" id="ip" name="ip" required><br>
            <label for="idmysonet">ID MySonet :</label>
            <input type="text" id="idmysonet" name="idmysonet" required><br>
            <input type="submit" value="Se connecter">
        </form>
        <?php
            } else {
        ?>
        <h2>Recherche d'ami</h2>
        <form action="recherche.php" method="post">
            <label for="pseudo">Pseudo :</label>
            <input type="text" id="pseudo" name="pseudo" required><br>
            <input type="submit" value="Rechercher">
        </form>
        <?php
            }
        ?>
    </div>
</body>
</html>
