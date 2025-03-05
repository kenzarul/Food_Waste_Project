<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in as a recipient
if (!isset($_SESSION['recipient_id'])) {
    header("Location: login.php");
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
</head>
<body>
    <h2>Profil du Bénéficiaire</h2>
    <p>Nom: <?php echo htmlspecialchars($recipient['nom']); ?></p>
    <p>Email: <?php echo htmlspecialchars($recipient['mail']); ?></p>
    <p>Téléphone: <?php echo htmlspecialchars($recipient['telephone']); ?></p>

    <!-- Buttons for actions -->
    <a href="food_listing.php">
        <button>Voir les annonces</button>
    </a>
    <a href="reservation_history.php">
        <button>Voir l'historique des réservations</button>
    </a>
</body>
</html>
