<?php
session_start();
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'student') {
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

$message = "";

// Handle new post
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    $msg = trim($_POST['message']);
    $stmt = $conn->prepare("INSERT INTO discussions (student_name, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $msg);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Message posted!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Fetch all discussions
$result = $conn->query("SELECT * FROM discussions ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Join Discussions - RCTI</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="#">R C TECHNICAL INSTITUTE</a>
    <div class="ms-auto">
        <span class="navbar-text text-white me-3">Welcome, <?php echo htmlspecialchars($name); ?> (Student)</span>
        <a href="Landing_page.html" class="btn btn-outline-light">Logout</a>
    </div>
</nav>

<div class="container mt-5">
    <h3>Join Discussions</h3>
    <?php echo $message; ?>
    
    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="message" class="form-label">Post a message</label>
            <textarea name="message" class="form-control" rows="3" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Post</button>
    </form>

    <h5 class="mb-3">Recent Messages:</h5>
    <div class="list-group">
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="list-group-item">
                <strong><?php echo htmlspecialchars($row['student_name']); ?></strong>
                <small class="text-muted float-end"><?php echo $row['created_at']; ?></small>
                <p class="mb-1"><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
            </div>
        <?php endwhile; ?>
    </div>
    <div class="mt-4">
      <button onclick="window.history.back();" class="btn btn-secondary">Back</button>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>
