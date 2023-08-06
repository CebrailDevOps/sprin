<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Inscription MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <?php if (isset($_SESSION['pseudo'])) {echo $_SESSION['pseudo'].' - ';} ?>MySoNet.Online
    </div>
    <div class="container">
        <form action="inscrire.php" method="post">
            <label for="pseudo">Pseudo:</label><br>
            <input type="text" id="pseudo" name="pseudo" minlength="5" required><br>
            <label for="ip">IP:</label><br>
            <input type="text" id="ip" name="ip" required><br>
            <label for="id">ID MySoNet:</label><br>
            <input type="text" id="idmysonet" name="idmysonet" required><br>
            <input type="submit" value="S'inscrire">
        </form>
    </div>
    <div class="content">
        <div><a href="index.php">Retour</a></div>
    </div>
</body>
</html>