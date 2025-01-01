<?php
// Conexiunea la baza de date
include 'flickscore_db.php';

// Determinăm pagina curentă
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? intval($_GET['page']) : 1;
$moviesPerPage = 10; // Numărul de filme pe pagină
$offset = ($page - 1) * $moviesPerPage;

// Obținem criteriul de sortare și genul selectat
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest'; // Implicit "Cele mai noi"
$genreFilter = isset($_GET['genre']) && $_GET['genre'] !== '' ? $_GET['genre'] : null;

// Mapăm criteriile de sortare la SQL
$sortQuery = "ORDER BY release_date DESC"; // Default: Cele mai noi
if ($sort === 'rating') {
    $sortQuery = "ORDER BY avg_rating DESC";
} elseif ($sort === 'oldest') {
    $sortQuery = "ORDER BY release_date ASC";
}

// Construim condiția pentru gen (dacă este selectat un gen)
$genreCondition = "";
if ($genreFilter) {
    $genreCondition = "WHERE genre = '" . $conn->real_escape_string($genreFilter) . "'";
}

// Obținem numărul total de filme pentru genul selectat
$totalMoviesResult = $conn->query("SELECT COUNT(*) AS total FROM movies $genreCondition");
$totalMovies = $totalMoviesResult->fetch_assoc()['total'] ?? 0;

// Calculăm numărul total de pagini
$totalPages = ceil($totalMovies / $moviesPerPage);

// Obținem filmele pentru pagina curentă, gen și sortare
$sql = "SELECT * FROM movies $genreCondition $sortQuery LIMIT $offset, $moviesPerPage";
$result = $conn->query($sql);

if (!$result) {
    die("Eroare la executarea interogării: " . $conn->error);
}

// Obținem toate genurile pentru dropdown
$genresResult = $conn->query("SELECT DISTINCT genre FROM movies");
$genres = [];
while ($row = $genresResult->fetch_assoc()) {
    $genres[] = $row['genre'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FlickScore</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <a href="index.php" style="text-decoration: none; color: inherit;">
        <h1>FlickScore</h1>
    </a>
    <a href="login.php" class="btn">Admin Login</a>
</header>
    <main>
        <!-- Formular pentru sortare și filtrare -->
        <form method="GET" class="sort-form">
            <label for="genre">Gen:</label>
            <select name="genre" id="genre" onchange="this.form.submit()">
                <option value="">Toate genurile</option>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?php echo htmlspecialchars($genre); ?>" <?php if ($genre === $genreFilter) echo 'selected'; ?>>
                        <?php echo htmlspecialchars($genre); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="sort">Sortează după:</label>
            <select name="sort" id="sort" onchange="this.form.submit()">
                <option value="newest" <?php if ($sort === 'newest') echo 'selected'; ?>>Cele mai noi</option>
                <option value="rating" <?php if ($sort === 'rating') echo 'selected'; ?>>Cele mai bine cotate</option>
                <option value="oldest" <?php if ($sort === 'oldest') echo 'selected'; ?>>Cele mai vechi</option>
            </select>
        </form>

        <div class="movies-container">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="movie-card">';
                    echo '<img src="images/' . htmlspecialchars($row['poster']) . '" alt="' . htmlspecialchars($row['title']) . '">';
                    echo '<h3>' . htmlspecialchars($row['title']) . '</h3>';
                    echo '<p>' . htmlspecialchars($row['genre']) . '</p>';
                    echo '<p>Rating: ' . number_format($row['avg_rating'], 1) . '/10</p>';
                    echo '<a href="movie.php?id=' . $row['id'] . '" class="btn">Detalii</a>';
                    echo '</div>';
                }
            } else {
                echo '<p>Nu există filme de afișat.</p>';
            }
            ?>
        </div>

        <footer>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="index.php?page=<?php echo $page - 1; ?>&sort=<?php echo htmlspecialchars($sort); ?>&genre=<?php echo htmlspecialchars($genreFilter); ?>" class="btn">Pagina Anterioară</a>
                <?php endif; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="index.php?page=<?php echo $page + 1; ?>&sort=<?php echo htmlspecialchars($sort); ?>&genre=<?php echo htmlspecialchars($genreFilter); ?>" class="btn">Următoarea pagină</a>
                <?php endif; ?>
            </div>
        </footer>
    </main>
</body>
</html>
