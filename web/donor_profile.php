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
$sql = "SELECT * FROM recipient_view WHERE id_donor = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_donor);
$stmt->execute();
$result = $stmt->get_result();
$donor = $result->fetch_assoc();

// Fetch donor's listings along with recipient names, pickup time, and feedback
$listings_sql = "
    SELECT l.id_list, l.type, l.description, l.quantité, l.date_expire, l.STATUS, 
           r.nom AS recipient_name, res.pickup_time, f.rating, f.commentaire
    FROM listing l
    LEFT JOIN reservation res ON l.id_list = res.id_list
    LEFT JOIN recipient r ON res.id_rec = r.id_rec
    LEFT JOIN feedback f ON res.id_reserve = f.id_reserve
    WHERE l.id_donor = ?
";
$listings_stmt = $conn->prepare($listings_sql);
$listings_stmt->bind_param("i", $id_donor);
$listings_stmt->execute();
$listings_result = $listings_stmt->get_result();

// Food animation images
$foodImages = ["chicken1.png", "steak.png", "salad.png", "noodle.png"];
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
    <div class="food-container">
        <div class="food-container-inner">
            <?php for ($i = 0; $i < 27; $i++): ?>
                <div class="food-image" style="background-image: url('../static/img/<?php echo htmlspecialchars($foodImages[array_rand($foodImages)]); ?>');"></div>
            <?php endfor; ?>
        </div>
    </div>

    <div class="donateur-container">
        <h2>Bienvenue <?php echo htmlspecialchars($donor['donor_name']); ?></h2>
        <div class="donor-details">
            <p><strong>Email :</strong> <?php echo htmlspecialchars($donor['mail']); ?></p>
            <p><strong>Téléphone :</strong> <?php echo htmlspecialchars($donor['telephone']); ?></p>
            
            <?php if (!empty($donor['nom_etablissement'])): ?>
                <p><strong>Établissement :</strong> <?php echo htmlspecialchars($donor['nom_etablissement']); ?></p>
            <?php endif; ?>
        </div>

        <h2>Vos Annonces</h2>
        <div class="ajout-listing">
            <a href="create_listing.php">
                <button>Ajout Annonce</button>
            </a>
        </div>

        <table border="1">
            <tr>
                <th>Type</th>
                <th>Description</th>
                <th>Quantité</th>
                <th>Date d'expiration</th>
                <th>Statut</th>
                <th>Bénéficiaire</th>
                <th>Heure de Retrait</th>
                <th>Feedback</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $listings_result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                    <td><?php echo htmlspecialchars($row['quantité']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_expire']); ?></td>
                    <td><?php echo htmlspecialchars($row['STATUS']); ?></td>
                    <td><?php echo $row['recipient_name'] ? htmlspecialchars($row['recipient_name']) : "Aucun bénéficiaire"; ?></td>
                    <td><?php echo $row['pickup_time'] ? htmlspecialchars($row['pickup_time']) : "Non défini"; ?></td>
                    <td>
                        <?php 
                            if ($row['rating'] !== null && $row['commentaire'] !== null) {
                                echo "Note: " . htmlspecialchars($row['rating']) . "<br>Commentaire: " . htmlspecialchars($row['commentaire']);
                            } else {
                                echo "Pas de feedback";
                            }
                        ?>
                    </td>
                    <td>
                        <a href="edit_listing.php?id=<?php echo $row['id_list']; ?>">Modifier</a> |
                        <a href="delete_listing.php?id=<?php echo $row['id_list']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette annonce ?');">Supprimer</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
        
        <a href="logout.php"><button type="submit">Se Déconnecter</button></a>
    </div>
</body>
</html>
