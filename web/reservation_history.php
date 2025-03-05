<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in as a recipient
if (!isset($_SESSION['recipient_id'])) {
    header("Location: login.php");
    exit();
}

$id_rec = $_SESSION['recipient_id'];

// Fetch past reservations
$reservations_sql = "SELECT l.type, l.description, r._date, r.pickup_time, r.STATUS 
                     FROM reservation r 
                     JOIN listing l ON r.id_list = l.id_list 
                     WHERE r.id_rec = ?";
$reservations_stmt = $conn->prepare($reservations_sql);
$reservations_stmt->bind_param("i", $id_rec);
$reservations_stmt->execute();
$reservations_result = $reservations_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Historique des Réservations</title>
</head>
<body>
    <h2>Historique des Réservations</h2>
    
    <?php if ($reservations_result->num_rows > 0) { ?>
        <table border="1">
            <tr>
                <th>Type</th>
                <th>Description</th>
                <th>Date</th>
                <th>Heure de retrait</th>
                <th>Statut</th>
            </tr>
            <?php while ($row = $reservations_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['_date']); ?></td>
                    <td><?php echo htmlspecialchars($row['pickup_time']); ?></td>
                    <td><?php echo htmlspecialchars($row['STATUS']); ?></td>
                </tr>
            <?php } ?>
        </table>
    <?php } else { ?>
        <p>Aucune réservation trouvée.</p>
    <?php } ?>

    <br>
    <a href="recipient_profile.php">
        <button>Retour au profil</button>
    </a>
</body>
</html>
