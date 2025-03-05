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
<?php
    // Include the same animation as register.php
    $foodImages = ["chicken1.png", "steak.png", "salad.png", "noodle.png"];
    ?>
    <div class="food-container">
        <div class="food-container-inner">
            <?php for ($i = 0; $i < 27; $i++): ?>
                <div class="food-image" style="background-image: url('../static/img/<?php echo $foodImages[array_rand($foodImages)]; ?>');"></div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="login-container">
        <h2> Bienvenue <?php echo htmlspecialchars($donor['nom']); ?></h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <div class="donor-details">
            <p><strong>Votre Établissement :</strong> <?php echo htmlspecialchars($donor['nom_etablissement']); ?></p>
            <p><strong>Votre adresse mail : </strong><?php echo htmlspecialchars($donor['mail']); ?></p>
            <p><strong>Votre numéro téléphone :</strong> <?php echo htmlspecialchars($donor['telephone']); ?></p>
        </div>
        <h3>Vos Annonces</h3>
    <a href="create_listing.php">
        <button>Créer une nouvelle annonce</button>
    </a>

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
    <a href="logout.php"><button type="submit">Se déconnecter</button></a>
    </div>

    
</body>
</html>
