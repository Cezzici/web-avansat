<?php
include 'flickscore_db.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM movies WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin.php?message=Filmul a fost șters cu succes!");
        exit();
    } else {
        echo "Eroare la ștergerea filmului: " . $conn->error;
    }
} else {
    echo "ID invalid!";
}
?>
