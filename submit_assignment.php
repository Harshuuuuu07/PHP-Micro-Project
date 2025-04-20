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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $assignment_name = $_POST['assignment_name'];
    $subject = $_POST['subject'];

    // Handle file upload
    $target_dir = "Assisgnment/"; // Relative path to store uploaded files
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $uploadOk = 1;
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if file is a valid format
    if ($fileType != "pdf" && $fileType != "docx" && $fileType != "pptx") {
        $message = "<div class='alert alert-danger'>Sorry, only PDF, DOCX & PPTX files are allowed.</div>";
        $uploadOk = 0;
    }

    // Check file size (limit to 10MB)
    if ($_FILES["file"]["size"] > 10485760) { // 10MB
        $message = "<div class='alert alert-danger'>Sorry, your file is too large. Max size allowed is 10MB.</div>";
        $uploadOk = 0;
    }

    // If no errors, try to upload file
    if ($uploadOk == 1) {
        // Create uploads directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            // Save the assignment details in the database
            $stmt = $conn->prepare("INSERT INTO submitted_assignments (student_name, assignment_name, subject, file_path) VALUES (?, ?, ?, ?)");
            
            // Check if prepare() failed
            if ($stmt === false) {
                $message = "<div class='alert alert-danger'>Error preparing statement: " . $conn->error . "</div>";
            } else {
                $stmt->bind_param("ssss", $name, $assignment_name, $subject, $target_file);

                if ($stmt->execute()) {
                    $message = "<div class='alert alert-success'>Assignment submitted successfully.</div>";
                } else {
                    $message = "<div class='alert alert-danger'>Error submitting assignment: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
        } else {
            $message = "<div class='alert alert-danger'>Sorry, there was an error uploading your file.</div>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Assignment - RCTI</title>
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
        <h3>Submit Your Assignment</h3>
        <?php echo $message; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="assignment_name" class="form-label">Assignment Name</label>
                <input type="text" class="form-control" name="assignment_name" required>
            </div>
            <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" class="form-control" name="subject" required>
            </div>
            <div class="mb-3">
                <label for="file" class="form-label">Choose Assignment File</label>
                <input type="file" class="form-control" name="file" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">Submit Assignment</button>
        </form>
        <div class="mt-4">
            <button onclick="window.history.back();" class="btn btn-secondary">Back</button>
        </div>
    </div>
    
</body>
</html>

<?php $conn->close(); ?>
