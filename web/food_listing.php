<?php
include 'db_connect.php'; // Ensure database connection

// Fetch filter values from GET request
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$donor_filter = isset($_GET['donor']) ? $_GET['donor'] : '';
$date_filter = isset($_GET['date_expire']) ? $_GET['date_expire'] : '';

// Base SQL query
$sql = "SELECT l.id_list, l.type, l.description, l.quantité, l.date_expire, l.STATUS, d.nom_etablissement 
        FROM listing l 
        JOIN donateurs d ON l.id_donor = d.id_donor 
        WHERE l.STATUS = 'Available'";

// Apply filters if selected
if (!empty($type_filter)) {
    $sql .= " AND l.type = '" . $conn->real_escape_string($type_filter) . "'";
}
if (!empty($donor_filter)) {
    $sql .= " AND d.nom_etablissement = '" . $conn->real_escape_string($donor_filter) . "'";
}
if (!empty($date_filter)) {
    $sql .= " AND l.date_expire >= '" . $conn->real_escape_string($date_filter) . "'";
}

$result = $conn->query($sql);

// Fetch unique types and donors for filtering
$types_result = $conn->query("SELECT DISTINCT type FROM listing");
$donors_result = $conn->query("SELECT DISTINCT nom_etablissement FROM donateurs");
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

    <!-- Filter Section -->
    <form method="GET">
        <label for="type">Type:</label>
        <select name="type">
            <option value="">Tous</option>
            <?php while ($row = $types_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['type']; ?>" <?php if ($type_filter == $row['type']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($row['type']); ?>
                </option>
            <?php } ?>
        </select>

        <label for="donor">Donateur:</label>
        <select name="donor">
            <option value="">Tous</option>
            <?php while ($row = $donors_result->fetch_assoc()) { ?>
                <option value="<?php echo $row['nom_etablissement']; ?>" <?php if ($donor_filter == $row['nom_etablissement']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($row['nom_etablissement']); ?>
                </option>
            <?php } ?>
        </select>

        <label for="date_expire">Date d'expiration après:</label>
        <input type="date" name="date_expire" value="<?php echo htmlspecialchars($date_filter); ?>">

        <button type="submit">Filtrer</button>
        <a href="food_listing.php"><button type="button">Réinitialiser</button></a>
    </form>

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
