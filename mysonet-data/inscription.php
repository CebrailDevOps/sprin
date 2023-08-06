<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
    <title>Inscription MySoNet.Online</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="header">
        <?php if ($Get_SESSION['pseudo']) {echo $Get_SESSION['pseudo'].' - ';} ?>MySoNet.Online
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
</body>
</html>