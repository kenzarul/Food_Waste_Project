<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'food_saving');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $conn->real_escape_string($_POST['name']);
    $etablissement = $conn->real_escape_string($_POST['etablissement']);
    $email = $conn->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Secure password hashing
    $phone = $conn->real_escape_string($_POST['phone']);
    $role = $conn->real_escape_string($_POST['role']);

    // Insert into the appropriate table based on role
    $query = "INSERT INTO $role (name, etablissement, mail, password, phone) 
              VALUES ('$name', '$etablissement', '$email', '$password', '$phone')";

    if ($conn->query($query) === TRUE) {
        header('Location: index.php'); // Redirect to login after registration
        exit();
    } else {
        $error = "Erreur lors de l'inscription: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="../static/css/main.css">
</head>
<body>

    <?php
    $foodImages = ["chicken1.png", "steak.png", "salad.png", "noodle.png"];
    ?>

    <div class="food-container">
        <div class="food-container-inner">
            <?php for ($i = 0; $i < 27; $i++): ?>
                <div class="food-image" style="background-image: url('../static/img/<?php echo $foodImages[array_rand($foodImages)]; ?>');"></div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="register-container">
        <h2>Créer un compte</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Nom" required>
            <input type="text" name="etablissement" placeholder="Etablissement" required>
            <input type="email" name="email" placeholder="E-Mail" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <input type="text" name="phone" placeholder="Téléphone" required>
            <select name="role">
                <option value="recipient">Bénéficiaire</option>
                <option value="donateurs">Donateur</option>
            </select>
            <button type="submit">S'inscrire</button>
        </form>
        <p class="login-link">Déjà un compte ? <a href="../index.php">Se connecter</a></p>
    </div>
</body>
</html>