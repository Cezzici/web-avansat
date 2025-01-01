<?php
session_start();

include 'flickscore_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Exemplu de autentificare (trebuie să ai un tabel `admins` în baza de date)
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $_SESSION['admin'] = $username;
        header('Location: admin.php');
        exit;
    } else {
        $error = "Utilizator sau parolă incorecte!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FlickScore Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-form">
        <h2>Autentificare Admin</h2>
        <?php if (isset($error)): ?>
            <p class="error-message"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="POST">
            <label for="username">Utilizator:</label>
            <input type="text" id="username" name="username" required>
            <label for="password">Parolă:</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Autentificare</button>
        </form>
    </div>
</body>
</html>
