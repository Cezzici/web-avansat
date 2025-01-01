<?php
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "flickscore";


$conn = new mysqli($servername, $username, $password, $dbname, 3307);


if ($conn->connect_error) {
    die("Conexiunea la baza de date a eșuat: " . $conn->connect_error);
}
?>
