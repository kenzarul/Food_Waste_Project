<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in as a recipient
if (!isset($_SESSION['recipient_id'])) {
    header("Location: login.php");
    exit();
}

$id_rec = $_SESSION['recipient_id'];
$id_reserve = isset($_GET['id_reserve']) ? intval($_GET['id_reserve']) : 0;
$message = ""; // Variable to store messages
$form_disabled = false; // Variable to control form disable status

// Check if the reservation exists for the recipient
$check_sql = "SELECT * FROM reservation WHERE id_reserve = ? AND id_rec = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ii", $id_reserve, $id_rec);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    echo "Réservation introuvable.";
    exit();
}

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['rating'], $_POST['commentaire'])) {
    $rating = intval($_POST['rating']);
    $commentaire = $conn->real_escape_string($_POST['commentaire']);

    // Insert feedback into the feedback table
    $feedback_sql = "INSERT INTO feedback (id_reserve, rating, commentaire) 
                     VALUES (?, ?, ?)";
    $feedback_stmt = $conn->prepare($feedback_sql);
    $feedback_stmt->bind_param("iis", $id_reserve, $rating, $commentaire);

    if ($feedback_stmt->execute()) {
        $message = "Feedback ajouté avec succès!"; // Success message
        $form_disabled = true; // Disable the form after feedback is submitted
    } else {
        $message = "Erreur lors de l'ajout du feedback."; // Error message
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Donner un Feedback</title>
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

    <div class="annonce-container">
        <h2>Donner un Feedback</h2>
        <?php if (!empty($message)): ?>
            <p class="message"><?php echo $message; ?></p> 
        <?php endif; ?>

        <form method="POST">
            <label for="rating">Évaluation (1 à 5):</label>
            <input type="number" name="rating" min="1" max="5" required <?php echo $form_disabled ? 'disabled' : ''; ?>>
            <br><br>
            <label for="commentaire">Commentaire:</label>
            <textarea name="commentaire" required <?php echo $form_disabled ? 'disabled' : ''; ?>></textarea>
            <br><br>
            <button type="submit" <?php echo $form_disabled ? 'disabled' : ''; ?>>Soumettre le Feedback</button>
        </form>

        <a href="reservation_history.php"><button type="submit">Retour à l'historique des réservations</button></a>
    </div>
</body>
</html>
