<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'food_saving');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mail = $conn->real_escape_string($_POST['mail']);
    $password = $conn->real_escape_string($_POST['password']);
    $role = $conn->real_escape_string($_POST['role']);

    $query = "SELECT * FROM $role WHERE mail='$mail' AND password='$password'";
    $result = $conn->query($query);
    
    if ($result->num_rows > 0) {
        $_SESSION['user'] = $mail;
        $_SESSION['role'] = $role;
        header('Location: dashboard.php');
        exit();
    } else {
        $error = 'Identifiants incorrects';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="static/css/main.css">
</head>
<body>

    <?php
    $foodImages = ["chicken1.png", "steak.png", "salad.png", "noodle.png"];
    ?>

    <div class="food-container">
        <div class="food-container-inner">
            <?php for ($i = 0; $i < 27; $i++): ?>
                <div class="food-image" style="background-image: url('static/img/<?php echo $foodImages[array_rand($foodImages)]; ?>');"></div>
            <?php endfor; ?>
        </div>
    </div>


    <div class="login-container">
        <h2>Connexion</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="email" name="mail" placeholder="E-Mail" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <select name="role">
                <option value="recipient">Bénéficiaire</option>
                <option value="donateurs">Donateur</option>
            </select>
            <button type="submit">Se connecter</button>
            <p class="register-link">Pas encore de compte ? <a href="web/register.php">Créer un compte</a></p>
        </form>
    </div>
</body>
</html>