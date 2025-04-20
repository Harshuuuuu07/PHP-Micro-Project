<?php
session_start();
if (!isset($_SESSION['name']) || $_SESSION['role'] !== 'teacher') {
    header("Location: login.html");
    exit();
}

$teacher_name = $_SESSION['name'];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users";
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

// Handle grade submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $student_name = $_POST["student_name"];
    $subject = $_POST["subject"];
    $grade = $_POST["grade"];
    $semester = $_POST["semester"];
    $remarks = $_POST["remarks"];

    $stmt = $conn->prepare("INSERT INTO grades (student_name, subject, grade, semester, remarks) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $student_name, $subject, $grade, $semester, $remarks);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Grade submitted successfully.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }

    $stmt->close();
}

// Fetch distinct students from student table
$students = [];
$student_result = $conn->query("SELECT DISTINCT Name FROM student");
if ($student_result) {
    while ($row = $student_result->fetch_assoc()) {
        $students[] = $row['Name'];
    }
}

// Fetch all submitted grades
$grades = [];
$grades_result = $conn->query("SELECT * FROM grades ORDER BY id DESC");
if ($grades_result) {
    while ($row = $grades_result->fetch_assoc()) {
        $grades[] = $row;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Generate Reports</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="#">R C TECHNICAL INSTITUTE</a>
    <div class="ms-auto">
        <span class="navbar-text text-white me-3">Welcome, <?php echo htmlspecialchars($teacher_name); ?> (Teacher)</span>
        <a href="Landing_page.html" class="btn btn-outline-light">Logout</a>
    </div>
</nav>

<div class="container mt-5">
    <h3>Grade Submission</h3>
    <?php echo $message; ?>
    <form method="POST" class="card p-4 mb-4 shadow">
        <div class="mb-3">
            <label for="student_name">Student Name</label>
            <select name="student_name" class="form-control" required>
                <option value="" disabled selected>Select a student</option>
                <?php foreach ($students as $student): ?>
                    <option value="<?php echo htmlspecialchars($student); ?>"><?php echo htmlspecialchars($student); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Subject</label>
            <input type="text" class="form-control" name="subject" required>
        </div>
        <div class="mb-3">
            <label>Grade</label>
            <input type="text" class="form-control" name="grade" required>
        </div>
        <div class="mb-3">
            <label>Semester</label>
            <input type="number" class="form-control" name="semester" required>
        </div>
        <div class="mb-3">
            <label>Remarks</label>
            <textarea class="form-control" name="remarks"></textarea>
        </div>
        <button type="submit" class="btn btn-success">Submit Grade</button>
    </form>

    <h4>All Submitted Grades</h4>
    <?php if (count($grades) > 0): ?>
        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Student Name</th>
                    <th>Subject</th>
                    <th>Grade</th>
                    <th>Semester</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($grades as $g): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($g['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($g['subject']); ?></td>
                        <td><?php echo htmlspecialchars($g['grade']); ?></td>
                        <td><?php echo htmlspecialchars($g['semester']); ?></td>
                        <td><?php echo htmlspecialchars($g['remarks']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="alert alert-info">No grades submitted yet.</div>
    <?php endif; ?>

    <div class="mt-4">
        <button onclick="window.history.back();" class="btn btn-secondary">Back</button>
    </div>
</div>
</body>
</html>

<?php $conn->close(); ?>
