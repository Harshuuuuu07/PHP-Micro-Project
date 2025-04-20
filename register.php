<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "users";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];

    // Password validation
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        // Check if email is already registered
        if ($role === 'teacher') {
            $check_stmt = $conn->prepare("SELECT Email FROM teachers WHERE Email = ?");
        } elseif ($role === 'student') {
            $check_stmt = $conn->prepare("SELECT Email FROM student WHERE Email = ?");
        } else {
            echo "<script>alert('Invalid role.');</script>";
            exit();
        }

        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            echo "<script>alert('Email already registered.');</script>";
        } else {
            // Insert data into respective table based on role
            if ($role === 'teacher') {
                $number = $_POST['number'];
                $subject = $_POST['subject'];
                $insert_stmt = $conn->prepare("INSERT INTO teachers (Name, Email, Number, Subject, password) VALUES (?, ?, ?, ?, ?)");
                $insert_stmt->bind_param("sssss", $name, $email, $number, $subject, $password);
            } elseif ($role === 'student') {
                $number = $_POST['number'];
                $sem = $_POST['sem'];
                $enroll = $_POST['enroll'];
                $insert_stmt = $conn->prepare("INSERT INTO student (Name, Email, Number, Sem, Enroll, password) VALUES (?, ?, ?, ?, ?, ?)");
                $insert_stmt->bind_param("sssiis", $name, $email, $number, $sem, $enroll, $password);
            }

            if ($insert_stmt->execute()) {
                $_SESSION['name'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $role; // Store role in session for use on Main.php
                header("Location: Main.php");
                exit();
            } else {
                echo "<script>alert('Registration failed: " . $insert_stmt->error . "');</script>";
            }

            $insert_stmt->close();
        }
        $check_stmt->close();
    }
}
$conn->close();
?>
