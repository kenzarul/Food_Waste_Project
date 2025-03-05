<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}

$id_donor = $_SESSION['donor_id'];

// Fetch existing categories from the database
$categories_sql = "SELECT id_categorie, nom FROM categorie";
$result = $conn->query($categories_sql);

$categories = [];
if ($result->num_rows > 0) {
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

    $insert_sql = "INSERT INTO listing (id_donor, type, description, quantité, date_expire, STATUS) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("ississ", $id_donor, $type, $description, $quantite, $date_expire, $status);

    if ($stmt->execute()) {
        $listing_id = $stmt->insert_id;

        // Handle category selection
        if (!empty($_POST['category'])) {
            if ($_POST['category'] === 'new') {
                // Insert new category if user added one
                $new_category = trim($_POST['new_category']);
                if (!empty($new_category)) {
                    $insert_category_sql = "INSERT INTO categorie (nom) VALUES (?)";
                    $category_stmt = $conn->prepare($insert_category_sql);
                    $category_stmt->bind_param("s", $new_category);
                    $category_stmt->execute();
                    $category_id = $category_stmt->insert_id;
                }
            } else {
                // Use selected category
                $category_id = intval($_POST['category']);
            }

            // Insert relationship into peut_avoir table
            $insert_peut_avoir_sql = "INSERT INTO peut_avoir (id_list, id_categorie) VALUES (?, ?)";
            $peut_avoir_stmt = $conn->prepare($insert_peut_avoir_sql);
            $peut_avoir_stmt->bind_param("ii", $listing_id, $category_id);
            $peut_avoir_stmt->execute();
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

        <label for="category">Catégorie:</label>
        <select name="category" id="category" onchange="toggleNewCategory()">
            <option value="" disabled selected>Choisissez une catégorie</option>
            <?php foreach ($categories as $category) { ?>
                <option value="<?php echo $category['id_categorie']; ?>">
                    <?php echo htmlspecialchars($category['nom']); ?>
                </option>
            <?php } ?>
            <option value="new">Ajouter une nouvelle catégorie</option>
        </select>
        <br>

        <div id="new_category_div" style="display: none;">
            <label for="new_category">Nouvelle catégorie:</label>
            <input type="text" id="new_category" name="new_category">
            <br>
        </div>

        <button type="submit">Créer l'Annonce</button>
    </form>
    <br>
    <a href="donor_profile.php">Retour au profil</a>

    <script>
        function toggleNewCategory() {
            var categorySelect = document.getElementById("category");
            var newCategoryDiv = document.getElementById("new_category_div");

            if (categorySelect.value === "new") {
                newCategoryDiv.style.display = "block";
            } else {
                newCategoryDiv.style.display = "none";
            }
        }
    </script>
</body>
</html>
