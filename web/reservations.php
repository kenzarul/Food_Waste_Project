<table border="1">
    <tr>
        <th>Type</th>
        <th>Description</th>
        <th>Quantité</th>
        <th>Date d'expiration</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $reservations_result->fetch_assoc()) { ?>
        <tr>
            <td><?php echo htmlspecialchars($row['type']); ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><?php echo htmlspecialchars($row['quantité']); ?></td>
            <td><?php echo htmlspecialchars($row['date_expire']); ?></td>
            <td>
                <a href="delete_listing.php?id=<?php echo $row['id_reserve']; ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?');">Supprimer</a>
            </td>
        </tr>
    <?php } ?>
</table>
