<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}

$id_donor = $_SESSION['donor_id'];
$listings_sql = "SELECT * FROM listing WHERE id_donor = ?";
$listings_stmt = $conn->prepare($listings_sql);
$listings_stmt->bind_param("i", $id_donor);
$listings_stmt->execute();
$listings_result = $listings_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mes Annonces</title>
</head>
<body>
    <h2>Mes Annonces</h2>
    <table border="1">
        <tr>
            <th>Type</th>
            <th>Description</th>
            <th>Quantité</th>
            <th>Date d'expiration</th>
            <th>Statut</th>
        </tr>
        <?php while ($row = $listings_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['type']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['quantité']); ?></td>
                <td><?php echo htmlspecialchars($row['date_expire']); ?></td>
                <td><?php echo htmlspecialchars($row['STATUS']); ?></td>
            </tr>
        <?php } ?>
    </table>

    <br>
    <a href="create_listing.php">Créer une nouvelle annonce</a>
</body>
</html>
