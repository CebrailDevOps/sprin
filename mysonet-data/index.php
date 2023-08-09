<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <span></span><span><?php if (isset($_SESSION['pseudo'])) {echo $_SESSION['pseudo'].' - ';} ?>MySoNet.Online</span>
        <?php if (isset($_SESSION['pseudo'])): ?>
            <a href="logout.php" class="power-btn"><img src="logout.svg" alt="Logout"></a>
        <?php endif; ?>
    </div>
    <div class="content">
        <div><a href="inscription.php">S'inscrire</a></div>
        <div><a href="rechercher.php">Rechercher un ami</a></div>
    </div>
</body>
</html>