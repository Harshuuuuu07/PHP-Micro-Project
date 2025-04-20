<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'student') {
    header("Location: login.html");
    exit();
}

$name = $_SESSION['name'];
$role = $_SESSION['role'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users";
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Fetch assignments from the database
$stmt = $conn->prepare("SELECT * FROM assignments");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Download Assignments - RCTI</title>
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
        <h3>Download Assignments</h3>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered mt-4">
                <thead>
                    <tr>
                        <th>Assignment Name</th>
                        <th>Subject</th>
                        <th>Download</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['subject']); ?></td>
                            <td><a href="<?php echo htmlspecialchars($row['file_path']); ?>" class="btn btn-primary" download>Download</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                No assignments available for download.
            </div>
        <?php endif; ?>
        <div class="mt-4">
            <button onclick="window.history.back();" class="btn btn-secondary">Back</button>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>
