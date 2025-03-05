<?php
include 'db_connect.php'; // Ensure database connection

$sql = "SELECT l.id_list, l.type, l.description, l.quantité, l.date_expire, l.STATUS, d.nom_etablissement 
        FROM listing l 
        JOIN donateurs d ON l.id_donor = d.id_donor 
        WHERE l.STATUS = 'Available'"; // Show only available listings

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Annonces Alimentaires</title>
    <link rel="stylesheet" href="../static/css/main.css">
</head>
<body>
    <h2>Annonces Alimentaires Disponibles</h2>
    <table border="1">
        <tr>
            <th>Type</th>
            <th>Description</th>
            <th>Quantité</th>
            <th>Date d'expiration</th>
            <th>Donateur</th>
            <th>Réserver</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['type']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['quantité']); ?></td>
                <td><?php echo htmlspecialchars($row['date_expire']); ?></td>
                <td><?php echo htmlspecialchars($row['nom_etablissement']); ?></td>
                <td><a href="reserve.php?id=<?php echo $row['id_list']; ?>">Réserver</a></td>
            </tr>
        <?php } ?>
    </table>

    <br>
    <a href="recipient_profile.php">
        <button>Retour au profil</button>
    </a>
</body>
</html>
