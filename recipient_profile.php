<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['recipient_id'])) {
    header("Location: login.php");
    exit();
}

$id_rec = $_SESSION['recipient_id'];
$sql = "SELECT * FROM recipient WHERE id_rec = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_rec);
$stmt->execute();
$result = $stmt->get_result();
$recipient = $result->fetch_assoc();

$reservations_sql = "SELECT r.id_reserve, l.type, l.description, r._date, r.pickup_time, r.STATUS 
                     FROM reservation r 
                     JOIN listing l ON r.id_list = l.id_list 
                     WHERE r.id_rec = ?";
$reservations_stmt = $conn->prepare($reservations_sql);
$reservations_stmt->bind_param("i", $id_rec);
$reservations_stmt->execute();
$reservations_result = $reservations_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Recipient Profile</title>
</head>
<body>
    <h2>Recipient Profile</h2>
    <p>Name: <?php echo htmlspecialchars($recipient['nom']); ?></p>
    <p>Email: <?php echo htmlspecialchars($recipient['mail']); ?></p>
    <p>Phone: <?php echo htmlspecialchars($recipient['telephone']); ?></p>

    <h3>Your Reservations</h3>
    <table border="1">
        <tr>
            <th>Type</th>
            <th>Description</th>
            <th>Date</th>
            <th>Pickup Time</th>
            <th>Status</th>
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
</body>
</html>
