<?php
session_start();

$servername = "127.0.0.1";
$username = "root";
$password = "";
$dbname = "users";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check login credentials for teacher
    $stmt_teacher = $conn->prepare("SELECT Name FROM teachers WHERE email=? AND password=?");
    $stmt_student = $conn->prepare("SELECT Name FROM student WHERE email=? AND password=?");

    if (!$stmt_teacher || !$stmt_student) {
        die("SQL Prepare Error: " . $conn->error);
    }

    // Check teacher credentials
    $stmt_teacher->bind_param("ss", $email, $password);
    $stmt_teacher->execute();
    $stmt_teacher->store_result();

    // Check if teacher is found
    if ($stmt_teacher->num_rows > 0) {
        $stmt_teacher->bind_result($name);
        $stmt_teacher->fetch();

        $_SESSION['name'] = $name;
        $_SESSION['role'] = 'teacher';  // Manually set role as 'teacher'

        header("Location: Main.php");
        exit();
    } else {
        // Check student credentials if no teacher found
        $stmt_student->bind_param("ss", $email, $password);
        $stmt_student->execute();
        $stmt_student->store_result();

        if ($stmt_student->num_rows > 0) {
            $stmt_student->bind_result($name);
            $stmt_student->fetch();

            $_SESSION['name'] = $name;
            $_SESSION['role'] = 'student';  // Manually set role as 'student'

            header("Location: Main.php");
            exit();
        } else {
            echo "Invalid email or password.";
        }
    }

    // Close statements
    $stmt_teacher->close();
    $stmt_student->close();
}

$conn->close();
?>
