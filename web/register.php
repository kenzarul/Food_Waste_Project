<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'food_saving');

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $conn->real_escape_string($_POST['nom']);
    $prenom = isset($_POST['prenom']) ? $conn->real_escape_string($_POST['prenom']) : null;
    $mail = $conn->real_escape_string($_POST['mail']);
    $password = $conn->real_escape_string($_POST['password']); // No hashing
    $telephone = $conn->real_escape_string($_POST['telephone']);
    $role = $conn->real_escape_string($_POST['role']);
    $address = isset($_POST['address']) ? $conn->real_escape_string($_POST['address']) : null;
    $donor_type = isset($_POST['donor_type']) ? $_POST['donor_type'] : null;

    if ($role === 'donateurs') {
        $query = "INSERT INTO donateurs (nom, prenom, mail, telephone, mot_de_passe, address) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssssss", $nom, $prenom, $mail, $telephone, $password, $address);
        
        if ($stmt->execute()) {
            $donor_id = $conn->insert_id;

            if ($donor_type === 'establishment') {
                $nom_etablissement = $conn->real_escape_string($_POST['nom_etablissement']);
                $etablissement_telephone = $conn->real_escape_string($_POST['etablissement_telephone']);
                $etablissement_address = $conn->real_escape_string($_POST['etablissement_address']);

                $query = "INSERT INTO etablissement (nom, telephone, adresse, id_donor) VALUES (?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssi", $nom_etablissement, $etablissement_telephone, $etablissement_address, $donor_id);
                $stmt->execute();
            }
            
            header('Location: index.php');
            exit();
        } else {
            $error = "Erreur lors de l'inscription: " . $stmt->error;
        }
    } elseif ($role === 'recipient') {
        $query = "INSERT INTO recipient (nom, mail, mot_de_passe, telephone) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssss", $nom, $mail, $password, $telephone);
        $stmt->execute();
        header('Location: index.php');
        exit();
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
        <input type="text" name="nom" placeholder="Nom" required>
        <input type="text" name="prenom" placeholder="Prénom" id="prenom_field">
        <input type="email" name="mail" placeholder="E-Mail" required>
        <input type="password" name="password" placeholder="Mot de passe" required>
        <input type="text" name="telephone" placeholder="Téléphone" required>
        <input type="text" name="address" placeholder="Adresse" required>

        <select name="role" id="role_select">
            <option value="recipient">Bénéficiaire</option>
            <option value="donateurs">Donateur</option>
        </select>

        <div id="donor_type_section" style="display: none;">
            <label for="donor_type">Type de Donateur:</label>
            <select name="donor_type" id="donor_type">
                <option value="individual">Individuel</option>
                <option value="establishment">Établissement</option>
            </select>
        </div>

        <div id="establishment_fields" style="display: none;">
            <input type="text" name="nom_etablissement" placeholder="Nom de l'Établissement" id="etablissement_field">
            <input type="text" name="etablissement_telephone" placeholder="Téléphone de l'Établissement" id="etablissement_telephone_field">
            <input type="text" name="etablissement_address" placeholder="Adresse de l'Établissement" id="etablissement_address_field">
        </div>

        <button type="submit">S'inscrire</button>
    </form>
    <p class="login-link">Déjà un compte ? <a href="index.php">Se connecter</a></p>
</div>

<script>
    document.getElementById('role_select').addEventListener('change', function () {
        let isDonor = this.value === 'donateurs';
        document.getElementById('donor_type_section').style.display = isDonor ? 'block' : 'none';
        document.getElementById('establishment_fields').style.display = isDonor && document.getElementById('donor_type').value === 'establishment' ? 'block' : 'none';
    });

    document.getElementById('donor_type').addEventListener('change', function () {
        document.getElementById('establishment_fields').style.display = this.value === 'establishment' ? 'block' : 'none';
    });
</script>

</body>
</html>