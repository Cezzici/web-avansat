<?php
include 'flickscore_db.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : null;

if (!$id) {
    die("ID film invalid!");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $genre = $_POST['genre'];
    $release_date = $_POST['release_date'];

    $stmt = $conn->prepare("UPDATE movies SET title = ?, description = ?, genre = ?, release_date = ? WHERE id = ?");
    $stmt->bind_param("ssssi", $title, $description, $genre, $release_date, $id);

    if ($stmt->execute()) {
        header("Location: admin.php?message=Filmul a fost actualizat cu succes!");
        exit();
    } else {
        echo "Eroare la actualizarea filmului: " . $conn->error;
    }
}

$sql = "SELECT * FROM movies WHERE id = $id";
$result = $conn->query($sql);
$movie = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifică film</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <a href="admin.php" class="btn">Înapoi la admin</a>
    </header>
    <main>
        <form method="POST">
            <h2>Modifică filmul</h2>
            <label for="title">Titlu:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($movie['title']); ?>" required>
            <label for="description">Descriere:</label>
            <textarea id="description" name="description" required><?php echo htmlspecialchars($movie['description']); ?></textarea>
            <label for="genre">Gen:</label>
            <input type="text" id="genre" name="genre" value="<?php echo htmlspecialchars($movie['genre']); ?>" required>
            <label for="release_date">Data lansării:</label>
            <input type="date" id="release_date" name="release_date" value="<?php echo htmlspecialchars($movie['release_date']); ?>" required>
            <button type="submit" class="btn">Actualizează</button>
        </form>
    </main>
</body>
</html>
