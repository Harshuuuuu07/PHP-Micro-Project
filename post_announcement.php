<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "users");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $body = $_POST['body'];
    $posted_by = $_SESSION['name'];

    $stmt = $conn->prepare("INSERT INTO announcements (title, message, posted_by) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $body, $posted_by);
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Announcement posted successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error posting announcement: " . $stmt->error . "</div>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Post Announcement</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h2>Post New Announcement</h2>
    <?= $message ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Message</label>
            <textarea name="body" class="form-control" rows="4" required></textarea>
        </div>
        <button class="btn btn-primary">Post Announcement</button>
    </form>
</body>
</html>

<?php $conn->close(); ?>
