<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in as a donor
if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}

$id_donor = $_SESSION['donor_id'];

// Fetch donor details
$sql = "SELECT * FROM donateurs WHERE id_donor = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_donor);
$stmt->execute();
$result = $stmt->get_result();
$donor = $result->fetch_assoc();

// Fetch donor's listings
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Donateur</title>
    <link rel="stylesheet" href="../static/css/main.css">
</head>
<body>
    <h2>Profil Donateur</h2>
    <p><strong>Nom:</strong> <?php echo htmlspecialchars($donor['nom']); ?></p>
    <p><strong>Établissement:</strong> <?php echo htmlspecialchars($donor['nom_etablissement']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($donor['mail']); ?></p>
    <p><strong>Téléphone:</strong> <?php echo htmlspecialchars($donor['telephone']); ?></p>

    <h3>Vos Annonces</h3>
    <a href="create_listing.php">
        <button>Créer une nouvelle annonce</button>
    </a>
    <br><br>

    <table border="1">
        <tr>
            <th>Type</th>
            <th>Description</th>
            <th>Quantité</th>
            <th>Date d'expiration</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $listings_result->fetch_assoc()) { ?>
            <tr>
                <td><?php echo htmlspecialchars($row['type']); ?></td>
                <td><?php echo htmlspecialchars($row['description']); ?></td>
                <td><?php echo htmlspecialchars($row['quantité']); ?></td>
                <td><?php echo htmlspecialchars($row['date_expire']); ?></td>
                <td><?php echo htmlspecialchars($row['STATUS']); ?></td>
                <td>
                    <a href="edit_listing.php?id=<?php echo $row['id_list']; ?>">Modifier</a> |
                    <a href="delete_listing.php?id=<?php echo $row['id_list']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">Supprimer</a>
                </td>
            </tr>
        <?php } ?>
    </table>

    <br>
    <a href="logout.php">Se déconnecter</a>
</body>
</html>
