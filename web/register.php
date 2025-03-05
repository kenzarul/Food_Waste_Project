<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'food_saving');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $conn->real_escape_string($_POST['nom']);
    $prenom = isset($_POST['prenom']) ? $conn->real_escape_string($_POST['prenom']) : null;
    $nom_etablissement = isset($_POST['nom_etablissement']) ? $conn->real_escape_string($_POST['nom_etablissement']) : null;
    $mail = $conn->real_escape_string($_POST['mail']);
    $password = $conn->real_escape_string($_POST['password']); // No hashing
    $telephone = $conn->real_escape_string($_POST['telephone']);
    $role = $conn->real_escape_string($_POST['role']);

    // Insert into the correct table based on role
    if ($role === 'donateurs') {
        $query = "INSERT INTO donateurs (nom, prenom, nom_etablissement, mail, telephone, mot_de_passe) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $nom, $prenom, $nom_etablissement, $mail, $telephone, $password);
    } elseif ($role === 'recipient') {
        $query = "INSERT INTO recipient (nom, mail, mot_de_passe, telephone) 
                  VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $nom, $mail, $password, $telephone);
    } else {
        die('Invalid role selected.');
    }

    if ($stmt->execute()) {
        header('Location: index.php');
        exit();
    } else {
        $error = "Erreur lors de l'inscription: " . $stmt->error;
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
    // Include the same animation as register.php
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
            <input type="text" name="nom" placeholder="Nom" required>
            <input type="text" name="prenom" placeholder="Prénom" id="prenom_field">
            <input type="text" name="nom_etablissement" placeholder="Établissement" id="etablissement_field">
            <input type="email" name="mail" placeholder="E-Mail" required>
            <input type="password" name="password" placeholder="Mot de passe" required>
            <input type="text" name="telephone" placeholder="Téléphone" required>
            <select name="role" id="role_select">
                <option value="recipient">Bénéficiaire</option>
                <option value="donateurs">Donateur</option>
            </select>
            <button type="submit">S'inscrire</button>
        </form>
        <p class="login-link">Déjà un compte ? <a href="index.php">Se connecter</a></p>
    </div>

    <script>
        document.getElementById('role_select').addEventListener('change', function () {
            let isDonor = this.value === 'donateurs';
            document.getElementById('prenom_field').required = isDonor;
            document.getElementById('etablissement_field').required = isDonor;
            document.getElementById('prenom_field').style.display = isDonor ? 'block' : 'none';
            document.getElementById('etablissement_field').style.display = isDonor ? 'block' : 'none';
        });
    </script>

</body>
</html>
