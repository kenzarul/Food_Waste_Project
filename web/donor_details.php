<?php
include 'db_connect.php'; // Ensure database connection

// Ensure the donor ID is set in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "ID du donateur invalide.";
    exit();
}

$id_donor = intval($_GET['id']);

// Fetch donor details
$donor_sql = "SELECT * FROM donateurs WHERE id_donor = ?";
$donor_stmt = $conn->prepare($donor_sql);
$donor_stmt->bind_param("i", $id_donor);
$donor_stmt->execute();
$donor_result = $donor_stmt->get_result();

if ($donor_result->num_rows == 0) {
    echo "Donateur non trouvé.";
    exit();
}

$donor = $donor_result->fetch_assoc();

// Fetch feedback for the donor, using the correct table join
$feedback_sql = "
    SELECT f.rating, f.commentaire, r.nom 
    FROM feedback f
    JOIN reservation res ON f.id_reserve = res.id_reserve
    JOIN recipient r ON res.id_rec = r.id_rec
    JOIN listing l ON res.id_list = l.id_list
    WHERE l.id_donor = ?"; // Using listing table to get donor info
$feedback_stmt = $conn->prepare($feedback_sql);
$feedback_stmt->bind_param("i", $id_donor);
$feedback_stmt->execute();
$feedback_result = $feedback_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détails du Donateur</title>
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
    <div class="donateur-container">
        <h2>Détails du Donateur</h2>
        <div class="donor-details">
        <?php
    // Check if the donor is related to an establishment
    if (!empty($donor['nom_etablissement'])) {
        echo "<p><strong>Nom de l'Établissement:</strong> " . htmlspecialchars($donor['nom_etablissement']) . "</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($donor['mail']) . "</p>";
        echo "<p><strong>Téléphone:</strong> " . htmlspecialchars($donor['telephone']) . "</p>";
        echo "<p><strong>Adresse de l'Établissement:</strong> " . htmlspecialchars($donor['address']) . "</p>";
    } else {
        echo "<p><strong>Donateur:</strong> Individuel</p>";
        echo "<p><strong>Email:</strong> " . htmlspecialchars($donor['mail']) . "</p>";
        echo "<p><strong>Téléphone:</strong> " . htmlspecialchars($donor['telephone']) . "</p>";
    }
    ?>
        </div>
        <h3>Feedback des Bénéficiaires</h3>
    <?php if ($feedback_result->num_rows > 0) { ?>
        <table border="1">
            <tr>
                <th>Nom du Bénéficiaire</th>
                <th>Note</th>
                <th>Commentaire</th>
            </tr>
            <?php while ($row = $feedback_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nom']); ?></td>
                    <td><?php echo htmlspecialchars($row['rating']); ?></td>
                    <td><?php echo htmlspecialchars($row['commentaire']); ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>Aucun feedback trouvé pour ce donateur.</p>
    <?php } ?>

        
        <a href="recipient_profile.php"><button type="submit">Retour au profil</button></a>
    </div>
    
</body>
</html>