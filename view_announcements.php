<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.html");
    exit();
}

$name = $_SESSION['name'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch announcements
$sql = "SELECT * FROM announcements ORDER BY post_date DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Announcements - RCTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark px-4">
        <a class="navbar-brand text-white" href="#">R C TECHNICAL INSTITUTE</a>
        <div class="ms-auto">
            <span class="navbar-text text-white me-3">Welcome, <?php echo htmlspecialchars($name); ?> (Student)</span>
            <a href="Landing_page.html" class="btn btn-outline-light">Logout</a>
        </div>
    </nav>

    <div class="container mt-5">
        <h3 class="mb-4">📢 Announcements</h3>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "
                <div class='card mb-3 shadow-sm'>
                    <div class='card-body'>
                        <h5 class='card-title'>" . htmlspecialchars($row['title']) . "</h5>
                        <p class='card-text'>" . nl2br(htmlspecialchars($row['message'])) . "</p>
                        <small class='text-muted'>Posted on: " . $row['post_date'] . "</small>
                    </div>
                </div>";
            }
        } else {
            echo "<div class='alert alert-info'>No announcements available.</div>";
        }
        ?>
        <a href="Main.php" class="btn btn-secondary mt-4">Back</a>
    </div>
</body>
</html>

<?php $conn->close(); ?>
