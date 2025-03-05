<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'food_saving');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mail = $conn->real_escape_string($_POST['mail']);
    $password = $_POST['password']; // Do not escape password, it's not SQL input
    $role = $conn->real_escape_string($_POST['role']);

    // Ensure correct table names
    $table = ($role === 'recipient') ? 'recipient' : 'donateurs';

    // Use prepared statements to prevent SQL injection
    $query = "SELECT * FROM $table WHERE mail = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $mail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Ensure correct column name for password
        if (password_verify($password, $user['mot_de_passe']) || $user['mot_de_passe'] === $password) {
            $_SESSION['user'] = $mail;
            $_SESSION['role'] = $role;
            
            if ($role == 'recipient') {
                $_SESSION['recipient_id'] = $user['id_rec'];
                header('Location: recipient_profile.php');
            } else {
                $_SESSION['donor_id'] = $user['id_donor'];
                header('Location: donor_profile.php');
            }
            exit();
        } else {
            $error = 'Identifiants incorrects';
        }
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
    // Include the same animation as register.php
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
        </form>
        <p class="register-link">Pas encore de compte ? <a href="register.php">Créer un compte</a></p>
    </div>

</body>
</html>
