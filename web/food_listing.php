<?php
include 'db_connect.php'; // Ensure you have a database connection file

$sql = "SELECT l.id_list, l.type, l.description, l.quantité, l.date_expire, l.STATUS, d.nom_etablissement 
        FROM listing l 
        JOIN donateurs d ON l.id_donor = d.id_donor 
        WHERE l.STATUS = 'Available'"; // Display only available listings

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Listings</title>
</head>
<body>
    <h2>Available Food Listings</h2>
    <table border="1">
        <tr>
            <th>Type</th>
            <th>Description</th>
            <th>Quantity</th>
            <th>Expiry Date</th>
            <th>Donor</th>
            <th>Reserve</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['type']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['quantité']); ?></td>
                <td><?php echo htmlspecialchars($row['date_expire']); ?></td>
                <td><?php echo htmlspecialchars($row['nom_etablissement']); ?></td>
                <td><a href="reserve.php?id=<?php echo $row['id_list']; ?>">Reserve</a></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>
