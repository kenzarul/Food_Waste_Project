<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in as a donor
if (!isset($_SESSION['donor_id'])) {
    header("Location: login.php");
    exit();
}

$id_donor = $_SESSION['donor_id'];

// Check if the listing ID is passed
if (isset($_GET['id'])) {
    $listing_id = $_GET['id'];

    // Fetch the listing details to ensure it belongs to the logged-in donor
    $sql = "SELECT * FROM listing WHERE id_list = ? AND id_donor = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $listing_id, $id_donor);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Listing exists and belongs to the logged-in donor, proceed to delete
        $delete_sql = "DELETE FROM listing WHERE id_list = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $listing_id);
        $delete_stmt->execute();

        // Redirect to the donor profile page with success message
        header("Location: donor_profile.php?message=Annonce supprimée avec succès");
        exit();
    } else {
        // Listing not found or doesn't belong to the logged-in donor
        header("Location: donor_profile.php?error=Annonce introuvable ou non autorisée");
        exit();
    }
} else {
    // No listing ID provided
    header("Location: donor_profile.php?error=ID d'annonce manquant");
    exit();
}
?>
