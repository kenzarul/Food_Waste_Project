<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in as a donor
if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}

$id_donor = $_SESSION['donor_id'];

// Fetch reservations for the donor's listings
$reservations_sql = "
    SELECT r.id_reserve, r._date, r.pickup_time, r.STATUS, l.description, re.nom AS recipient_name
    FROM reservation r
    JOIN listing l ON r.id_list = l.id_list
    JOIN recipient re ON r.id_rec = re.id_rec
    WHERE l.id_donor = ?
";

$reservations_stmt = $conn->prepare($reservations_sql);
$reservations_stmt->bind_param("i", $id_donor);
$reservations_stmt->execute();
$reservations_result = $reservations_stmt->get_result();

// Handle reservation status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_reserve'], $_POST['new_status'])) {
    $id_reserve = intval($_POST['id_reserve']);
    $new_status = $_POST['new_status'];

    $update_sql = "UPDATE reservation SET STATUS = ? WHERE id_reserve = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $id_reserve);

    if ($update_stmt->execute()) {
        echo "Statut mis à jour avec succès!";
        header("Refresh:0"); // Reload page to reflect changes
        exit();
    } else {
        echo "Erreur lors de la mise à jour du statut.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gérer les Réservations</title>
    <link rel="stylesheet" href="../static/css/main.css">
</head>
<body>
    <h2>Gérer les Réservations</h2>

    <?php if ($reservations_result->num_rows > 0) { ?>
        <table border="1">
            <tr>
                <th>Annonce</th>
                <th>Bénéficiaire</th>
                <th>Date de réservation</th>
                <th>Heure de retrait</th>
                <th>Statut</th>
                <th>Action</th>
            </tr>
            <?php while ($reservation = $reservations_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($reservation['description']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['recipient_name']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['_date']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['pickup_time']); ?></td>
                    <td><?php echo htmlspecialchars($reservation['STATUS']); ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id_reserve" value="<?php echo $reservation['id_reserve']; ?>">
                            <select name="new_status">
                                <option value="Pending" <?php if ($reservation['STATUS'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                <option value="Validated" <?php if ($reservation['STATUS'] == 'Validated') echo 'selected'; ?>>Validated</option>
                                <option value="Completed" <?php if ($reservation['STATUS'] == 'Completed') echo 'selected'; ?>>Completed</option>
                                <option value="Cancelled" <?php if ($reservation['STATUS'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                            </select>
                            <button type="submit">Mettre à jour</button>
                        </form>
                    </td>
                </tr>
            <?php } ?>
        </table>
    <?php } else {
        echo "<p>Aucune réservation trouvée.</p>";
    } ?>

    <br>
    <a href="donor_profile.php">Retour au profil</a>
</body>
</html>
