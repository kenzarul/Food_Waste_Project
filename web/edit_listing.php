<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in as a donor
if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}

// Get the listing ID from the URL
if (isset($_GET['id'])) {
    $listing_id = $_GET['id'];

    // Fetch the listing to ensure it belongs to the logged-in donor
    $sql = "SELECT * FROM listing WHERE id_list = ? AND id_donor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $listing_id, $_SESSION['donor_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if the listing exists and belongs to the donor
    if ($result->num_rows > 0) {
        $listing = $result->fetch_assoc();
    } else {
        echo "Vous n'êtes pas autorisé à modifier cette annonce.";
        exit();
    }
} else {
    echo "ID de l'annonce non valide.";
    exit();
}

// Handle form submission to update the listing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $description = $_POST['description'];
    $quantité = $_POST['quantité'];
    $date_expire = $_POST['date_expire'];
    $status = $_POST['status'];

    // Update the listing with the new values
    $update_sql = "UPDATE listing SET type = ?, description = ?, quantité = ?, date_expire = ?, STATUS = ? WHERE id_list = ? AND id_donor = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssissii", $type, $description, $quantité, $date_expire, $status, $listing_id, $_SESSION['donor_id']);
    
    if ($update_stmt->execute()) {
        // Update the status of all related reservations
        $update_reservation_status_sql = "UPDATE reservation SET STATUS = ? WHERE id_list = ?";
        $update_reservation_stmt = $conn->prepare($update_reservation_status_sql);
        $update_reservation_stmt->bind_param("si", $status, $listing_id);
        $update_reservation_stmt->execute();

        header("Location: donor_profile.php"); // Redirect after successful update
        exit();
    } else {
        echo "Erreur lors de la mise à jour de l'annonce.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Annonce</title>
    <link rel="stylesheet" href="../static/css/main.css">
</head>
<body>
    <div class="annonce-container">
        <h2>Modifier Annonce</h2>
        <form action="edit_listing.php?id=<?php echo $listing_id; ?>" method="POST">
            <div class="form-group">
                <label for="type">Type :</label>
                <select name="type" id="type" required>
                    <option value="Alimentaire" <?php if ($listing['type'] == 'Alimentaire') echo 'selected'; ?>>Alimentaire</option>
                    <option value="Non-alimentaire" <?php if ($listing['type'] == 'Non-alimentaire') echo 'selected'; ?>>Non-alimentaire</option>
                </select><br><br>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" required><?php echo htmlspecialchars($listing['description']); ?></textarea><br><br>
            </div>

            <div class="form-group">
                <label for="quantite">Quantité:</label>
                <input type="number" name="quantité" id="quantité" value="<?php echo htmlspecialchars($listing['quantité']); ?>" required><br><br>
            </div>

            <div class="form-group">
                <label for="date_expire">Date d'expiration:</label>
                <input type="date" name="date_expire" id="date_expire" value="<?php echo htmlspecialchars($listing['date_expire']); ?>" required><br><br>
            </div>

            <div class="form-group">
                <label for="status">Statut:</label>
                <select name="status" id="status" required>
                    <option value="Pending" <?php if ($listing['STATUS'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Validated" <?php if ($listing['STATUS'] == 'Validated') echo 'selected'; ?>>Validated</option>
                    <option value="Completed" <?php if ($listing['STATUS'] == 'Completed') echo 'selected'; ?>>Completed</option>
                    <option value="Cancelled" <?php if ($listing['STATUS'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                    <option value="Available" <?php if ($listing['STATUS'] == 'Available') echo 'selected'; ?>>Available</option>
                </select><br><br>
            </div>

            <button type="submit">Mettre à jour</button>
        </form>
        <a href="donor_profile.php"><button type="submit">Retour au profil</button></a>
    </div>
</body>
</html>
