<?php
session_start();
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'teacher') {
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

// Handle form submission for new assignment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $subject = $_POST['subject'];
    $deadline = $_POST['deadline'];

    $file_path = "";
    if (!empty($_FILES["file"]["name"])) {
        $upload_dir = "Assisgnment_Teacher/";
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

        $file_path = $upload_dir . basename($_FILES["file"]["name"]);
        move_uploaded_file($_FILES["file"]["tmp_name"], $file_path);
    }

    $stmt = $conn->prepare("INSERT INTO assignments (title, subject, deadline, file_path, teacher_name) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdss", $title, $subject, $deadline, $file_path, $name);
    
    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Assignment created successfully.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}

// Fetch existing assignments
$assignments = [];
$result = $conn->query("SELECT * FROM assignments WHERE teacher_name = '$name' ORDER BY id DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $assignments[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Assignments - Teacher</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="#">R C TECHNICAL INSTITUTE</a>
    <div class="ms-auto">
        <span class="navbar-text text-white me-3">Welcome, <?php echo htmlspecialchars($name); ?> (Teacher)</span>
        <a href="Landing_page.html" class="btn btn-outline-light">Logout</a>
    </div>
</nav>

<div class="container mt-5">
    <h3>Manage Assignments</h3>
    <?php echo $message; ?>
    <form method="POST" enctype="multipart/form-data" class="card p-4 mb-4 shadow">
        <h5>Create New Assignment</h5>
        <div class="mb-3">
            <label>Title</label>
            <input type="text" class="form-control" name="title" required>
        </div>
        <div class="mb-3">
            <label>Subject</label>
            <input type="text" class="form-control" name="subject" required>
        </div>
        <div class="mb-3">
            <label>Deadline</label>
            <input type="date" class="form-control" name="deadline" required>
        </div>
        <div class="mb-3">
            <label>Upload File (optional)</label>
            <input type="file" class="form-control" name="file">
        </div>
        <button type="submit" class="btn btn-primary">Post Assignment</button>
    </form>

    <h5 class="mb-3">Your Posted Assignments</h5>
    <?php if (count($assignments) > 0): ?>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Title</th>
                    <th>Subject</th>
                    <th>Deadline</th>
                    <th>File</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $a): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($a['title']); ?></td>
                        <td><?php echo htmlspecialchars($a['subject']); ?></td>
                        <td><?php echo htmlspecialchars($a['deadline']); ?></td>
                        <td>
                            <?php if (!empty($a['file_path'])): ?>
                                <a href="<?php echo $a['file_path']; ?>" target="_blank">Download</a>
                            <?php else: ?>
                                N/A
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No assignments posted yet.</div>
    <?php endif; ?>
    <div class="mt-4">
        <button onclick="window.history.back();" class="btn btn-secondary">Back</button>
    </div>
</div>
</body>
</html>

<?php $conn->close(); ?>
