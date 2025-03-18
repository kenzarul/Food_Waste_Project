<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in as a recipient
if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'recipient') {
    // Redirect to the login page if the user is not logged in or doesn't have the 'recipient' role
    header('Location: index.php');
    exit();
}

$id_rec = $_SESSION['recipient_id'];

// Fetch recipient details
$sql = "SELECT * FROM recipient WHERE id_rec = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_rec);
$stmt->execute();
$result = $stmt->get_result();
$recipient = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Profil du Bénéficiaire</title>
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

    <div class="login-container">
        <h2> Bienvenue <?php echo htmlspecialchars($recipient['nom']); ?></h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <p><strong>Votre adresse mail </strong>: <?php echo htmlspecialchars($recipient['mail']); ?></p>
        <p><strong>Votre numéro téléphone : </strong><?php echo htmlspecialchars($recipient['telephone']); ?></p>
        <a href="food_listing.php"><button type="submit">Voir les annonces</button></a>
        <a href="reservation_history.php"><button type="submit">Voir l'historique des réservations</button></a>
        <a href="logout.php"><button type="submit">Se Deconnecter</button></a>

    </div>
    
</body>
</html>
