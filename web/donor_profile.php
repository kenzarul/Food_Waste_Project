<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}

$id_donor = $_SESSION['donor_id'];
$sql = "SELECT * FROM donateurs WHERE id_donor = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_donor);
$stmt->execute();
$result = $stmt->get_result();
$donor = $result->fetch_assoc();

$listings_sql = "SELECT * FROM listing WHERE id_donor = ?";
$listings_stmt = $conn->prepare($listings_sql);
$listings_stmt->bind_param("i", $id_donor);
$listings_stmt->execute();
$listings_result = $listings_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Profile</title>
</head>
<body>
    <h2>Donor Profile</h2>
    <p>Name: <?php echo htmlspecialchars($donor['nom']); ?></p>
    <p>Establishment: <?php echo htmlspecialchars($donor['nom_etablissement']); ?></p>
    <p>Email: <?php echo htmlspecialchars($donor['mail']); ?></p>
    <p>Phone: <?php echo htmlspecialchars($donor['telephone']); ?></p>

    <h3>Your Listings</h3>
    <table border="1">
        <tr>
            <th>Type</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Expiry Date</th>
            <th>Status</th>
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
</body>
</html>
