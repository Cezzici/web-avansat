<?php
include 'flickscore_db.php';

$id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : null;

if (!$id) {
    die("Filmul nu a fost găsit!");
}

// Obține detaliile filmului
$sql = "SELECT * FROM movies WHERE id = $id";
$result = $conn->query($sql);

if (!$result || $result->num_rows == 0) {
    die("Filmul nu a fost găsit!");
}

$movie = $result->fetch_assoc();

// Adăugarea unui rating
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = isset($_POST['rating']) && is_numeric($_POST['rating']) ? intval($_POST['rating']) : null;

    if ($rating && $rating >= 1 && $rating <= 10) {
        $stmt = $conn->prepare("INSERT INTO reviews (movie_id, rating, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("ii", $id, $rating);

        if ($stmt->execute()) {
            echo "<p style='color: green; text-align: center;'>Rating-ul a fost adăugat cu succes!</p>";
        } else {
            echo "<p style='color: red; text-align: center;'>Eroare la adăugarea rating-ului: " . $conn->error . "</p>";
        }

        $stmt->close();
    } else {
        echo "<p style='color: red; text-align: center;'>Te rog să introduci un rating valid (1-10).</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($movie['title']); ?> - FlickScore</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <a href="index.php" style="text-decoration: none; color: inherit;">
            <h1>FlickScore</h1>
        </a>
    </header>
    <main>
        <div class="movie-details">
            <img src="images/<?php echo htmlspecialchars($movie['poster']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
            <h2><?php echo htmlspecialchars($movie['title']); ?></h2>
            <p><strong>Gen:</strong> <?php echo htmlspecialchars($movie['genre']); ?></p>
            <p><strong>Data lansării:</strong> <?php echo htmlspecialchars($movie['release_date']); ?></p>
            <p><strong>Rating:</strong> <?php echo number_format($movie['avg_rating'], 1); ?>/10</p>
            <p><strong>Descriere:</strong> <?php echo htmlspecialchars($movie['description']); ?></p>
            <a href="index.php" class="btn">Înapoi la lista de filme</a>
        </div>

        <!-- Formular pentru rating -->
        <div class="rating-form">
            <h3>Lasă un rating:</h3>
            <form method="POST">
                <label for="rating">Rating (1-10):</label>
                <input type="number" id="rating" name="rating" min="1" max="10" required>
                <button type="submit">Trimite</button>
            </form>
        </div>
    </main>
</body>
</html>
