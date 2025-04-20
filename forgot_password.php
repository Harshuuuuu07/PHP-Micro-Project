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
    $email = $_POST['email'];
    $role = $_POST['role'];  // Get the role from the form (student or teacher)

    // Set the table and column name based on the role
    if ($role === 'student') {
        $table = 'student';
    } elseif ($role === 'teacher') {
        $table = 'teachers';
    } else {
        die("Invalid role selected.");
    }

    // Check if the email exists in the selected table
    $stmt = $conn->prepare("SELECT Email, password FROM $table WHERE Email=?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Fetch the user's email and password
        $stmt->bind_result($fetched_email, $original_password);
        $stmt->fetch();

        // Include PHPMailer
        require 'vendor/autoload.php';
        $mail = new PHPMailer\PHPMailer\PHPMailer();

        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '';
        $mail->Password = '';
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Debugging
        $mail->SMTPDebug = 2;
        $mail->Debugoutput = 'html';

        // For localhost testing only — remove in production!
        $mail->SMTPOptions = array(
            'ssl' => array(
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            )
        );

        // Email settings
        $mail->setFrom('', '');
        $mail->addAddress($fetched_email);
        $mail->Subject = 'Your Original Password';
        $mail->Body = "Hello,\n\nYour original password is: $original_password\n\nPlease login and change your password if needed.";

        // Send email
        if ($mail->send()) {
            echo "<script>
                alert('Your password has been sent to your email address.');
                window.location.href = 'login.html';
                </script>";
        } else {
            echo "<script>
                alert('Error sending email. Please try again later.');
                window.location.href = 'forgot_password.php';
                </script>";
        }
    } else {
        echo "<script>
            alert('⚠️ Email address not found.');
            window.location.href = 'forgot_password.php';
            </script>";
    }

    $stmt->close();
}

$conn->close();
?>
