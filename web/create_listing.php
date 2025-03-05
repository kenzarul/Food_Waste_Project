<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}

$id_donor = $_SESSION['donor_id'];

// Check table existence
$categories_sql = "SHOW TABLES LIKE 'categories'";
$categories_result = $conn->query($categories_sql);

$categories = [];
if ($categories_result->num_rows > 0) {
    // Fetch categories with correct column names
    $fetch_categories_sql = "SELECT id, nom_cat FROM categories"; // Change 'id' if needed
    $result = $conn->query($fetch_categories_sql);
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $description = $_POST['description'];
    $quantite = max(1, intval($_POST['quantite']));
    $date_expire = $_POST['date_expire'];
    $status = 'Available';

    $insert_sql = "INSERT INTO listing (id_donor, type, description, quantite, date_expire, STATUS) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ississ", $id_donor, $type, $description, $quantite, $date_expire, $status);

    if ($stmt->execute()) {
        $listing_id = $stmt->insert_id;

        if (!empty($_POST['categories']) && !empty($categories)) {
            foreach ($_POST['categories'] as $category_id) {
                $insert_category_sql = "INSERT INTO listing_categories (id_list, id_cat) VALUES (?, ?)";
                $category_stmt = $conn->prepare($insert_category_sql);
                $category_stmt->bind_param("ii", $listing_id, $category_id);
                $category_stmt->execute();
            }
        }

        echo "Annonce créée avec succès!";
    } else {
        echo "Erreur lors de la création de l'annonce: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une Annonce</title>
    <link rel="stylesheet" href="../static/css/main.css">
</head>
<body>
    <h2>Créer une Annonce</h2>
    <form method="POST">
        <label for="type">Type:</label>
        <select name="type" required>
            <option value="Alimentaire">Alimentaire</option>
            <option value="Non-Alimentaire">Non-Alimentaire</option>
        </select>
        <br>

        <label for="description">Description:</label>
        <textarea name="description" required></textarea>
        <br>

        <label for="quantite">Quantité:</label>
        <input type="number" id="quantite" name="quantite" min="1" required>
        <br>

        <label for="date_expire">Date d'expiration:</label>
        <input type="date" name="date_expire" required>
        <br>

        <?php if (!empty($categories)) { ?>
            <label>Catégories:</label><br>
            <?php foreach ($categories as $category) { ?>
                <input type="checkbox" name="categories[]" value="<?php echo $category['id']; ?>">
                <?php echo htmlspecialchars($category['nom_cat']); ?><br>
            <?php } ?>
            <br>
        <?php } else {
            echo "<p>Aucune catégorie disponible.</p>";
        } ?>

        <button type="submit">Créer l'Annonce</button>
    </form>
    <br>
    <a href="donor_profile.php">Retour au profil</a>
</body>
</html>
