<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.html");
    exit();
}

$conn = new mysqli("localhost", "root", "", "users");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $grade = $_POST['grade'];
    $remarks = $_POST['remarks'];
    $status = "graded";

    $stmt = $conn->prepare("UPDATE submitted_assignments SET grade=?, remarks=?, status=? WHERE id=?");
    $stmt->bind_param("sssi", $grade, $remarks, $status, $id);
    $stmt->execute();
    $stmt->close();
}

// Fetch all submissions
$result = $conn->query("SELECT * FROM submitted_assignments ORDER BY submission_date DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Grade Assignments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
    <h2>Grade Submissions</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student</th><th>Subject</th><th>Assignment</th><th>File</th><th>Status</th><th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['student_name']) ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= htmlspecialchars($row['assignment_name']) ?></td>
                    <td><a href="<?= $row['file_path'] ?>" target="_blank">Download</a></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="text" name="grade" placeholder="Grade" value="<?= $row['grade'] ?? '' ?>" class="form-control mb-1" required>
                            <input type="text" name="remarks" placeholder="Remarks" value="<?= $row['remarks'] ?? '' ?>" class="form-control mb-1">
                            <button class="btn btn-sm btn-success">Submit</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
    <div class="mt-4">
      <button onclick="window.history.back();" class="btn btn-secondary">Back</button>
    </div>
</body>
</html>

<?php $conn->close(); ?>
