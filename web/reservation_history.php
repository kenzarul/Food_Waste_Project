<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in as a recipient
if (!isset($_SESSION['recipient_id'])) {
    header("Location: login.php");
    exit();
}

$id_rec = $_SESSION['recipient_id'];

// Fetch past reservations with donor name
$reservations_sql = "SELECT r.id_reserve, l.type, l.description, r._date, r.pickup_time, r.STATUS, 
                            d.id_donor, COALESCE(e.nom, CONCAT(d.nom, ' ', d.prenom)) AS donateur_nom
                     FROM donor_reservations r 
                     JOIN listing l ON r.id_list = l.id_list 
                     JOIN donateurs d ON l.id_donor = d.id_donor
                     LEFT JOIN etablissement e ON d.id_donor = e.id_donor
                     WHERE r.id_rec = ?";
$reservations_stmt = $conn->prepare($reservations_sql);
$reservations_stmt->bind_param("i", $id_rec);
$reservations_stmt->execute();
$reservations_result = $reservations_stmt->get_result();

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating'], $_POST['commentaire'], $_POST['id_reserve'])) {
    $id_reserve = intval($_POST['id_reserve']);
    $rating = intval($_POST['rating']);
    $commentaire = $conn->real_escape_string($_POST['commentaire']);

    // Check if the reservation exists for the recipient
    $check_sql = "SELECT * FROM donor_reservations WHERE id_reserve = ? AND id_rec = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ii", $id_reserve, $id_rec);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        // Insert feedback
        $feedback_sql = "INSERT INTO feedback (id_reserve, rating, commentaire) 
                         VALUES (?, ?, ?)";
        $feedback_stmt = $conn->prepare($feedback_sql);
        $feedback_stmt->bind_param("iis", $id_reserve, $rating, $commentaire);

        if ($feedback_stmt->execute()) {
            echo "Feedback ajouté avec succès!";
        } else {
            echo "Erreur lors de l'ajout du feedback.";
        }
    } else {
        echo "Réservation introuvable.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Réservations</title>
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
    <h2> Historique des Réservations</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <?php if ($reservations_result->num_rows > 0) { ?>
        <table border="1">
            <tr>
                <th>Type</th>
                <th>Description</th>
                <th>Date</th>
                <th>Heure de retrait</th>
                <th>Statut</th>
                <th>Donateur</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $reservations_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['pickup_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['STATUS']); ?></td>
                    <td>
                        <a href="donor_details.php?id=<?php echo $row['id_donor']; ?>">
                            <?php echo htmlspecialchars($row['donateur_nom']); ?>
                        </a>
                    </td>
                    <td>
                        <?php 
                        // Check if feedback already exists for this reservation
                        $feedback_check_sql = "SELECT * FROM feedback WHERE id_reserve = ?";
                        $feedback_check_stmt = $conn->prepare($feedback_check_sql);
                        $feedback_check_stmt->bind_param("i", $row['id_reserve']);
                        $feedback_check_stmt->execute();
                        $feedback_check_result = $feedback_check_stmt->get_result();
                        
                        if ($feedback_check_result->num_rows == 0) { ?>
                            <a href="feedback_form.php?id_reserve=<?php echo $row['id_reserve']; ?>">Donner un feedback</a>
                        <?php } else { ?>
                            <span>Feedback déjà donné</span>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>Aucune réservation trouvée.</p>
    <?php } ?>

    <br>
    <a href="recipient_profile.php"><button type="submit">Retour au profil</button></a>

</div>
</body>
</html>
