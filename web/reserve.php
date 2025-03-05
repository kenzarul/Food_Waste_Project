<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in as a recipient
if (!isset($_SESSION['recipient_id'])) {
    header("Location: login.php");
    exit();
}

$id_rec = $_SESSION['recipient_id'];
$id_list = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Check if the listing exists and is available
$check_sql = "SELECT * FROM listing WHERE id_list = ? AND STATUS = 'Available'";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $id_list);
$check_stmt->execute();
$listing_result = $check_stmt->get_result();

if ($listing_result->num_rows == 0) {
    echo "Cette annonce n'est plus disponible.";
    exit();
}

// Insert reservation
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pickup_time = $conn->real_escape_string($_POST['pickup_time']);
    $date = date('Y-m-d'); // Current date

    $reserve_sql = "INSERT INTO reservation (id_rec, id_list, _date, pickup_time, STATUS) 
                    VALUES (?, ?, ?, ?, 'Pending')";
    $reserve_stmt = $conn->prepare($reserve_sql);
    $reserve_stmt->bind_param("iiss", $id_rec, $id_list, $date, $pickup_time);

    if ($reserve_stmt->execute()) {
        // Update listing status
        $update_sql = "UPDATE listing SET STATUS = 'Reserved' WHERE id_list = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $id_list);
        $update_stmt->execute();

        echo "Réservation réussie !";
        exit();
    } else {
        echo "Erreur lors de la réservation.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réserver une Annonce</title>
    <link rel="stylesheet" href="../static/css/main.css">
</head>
<body>
    <h2>Réserver une Annonce</h2>
    <form method="POST">
        <label>Heure de retrait :</label>
        <input type="time" name="pickup_time" required>
        <button type="submit">Réserver</button>
    </form>

    <br>
    <a href="food_listing.php">Retour aux annonces</a>
</body>
</html>
