<?php
session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['role'])) {
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
    if ($role === 'teacher') {
        $subject = $_POST['subject'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $stmt = $conn->prepare("UPDATE teachers SET Subject = ?, Email = ?, Number = ? WHERE Name = ?");
        $stmt->bind_param("ssss", $subject, $email, $phone, $name);
    } elseif ($role === 'student') {
        $class = $_POST['class'];
        $roll = $_POST['roll'];
        $email = $_POST['email'];
        $stmt = $conn->prepare("UPDATE student SET Sem = ?, Enroll = ?, Email = ? WHERE Name = ?");
        $stmt->bind_param("ssss", $class, $roll, $email, $name);
    }

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Information updated successfully.</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Dashboard - <?php echo ucfirst($role); ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .hover-shadow:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      transition: 0.3s ease;
    }
  </style>
</head>
<body>
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark px-4">
    <a class="navbar-brand" href="#">R C TECHNICAL INSTITUTE</a>
    <div class="ms-auto">
      <span class="navbar-text text-white me-3">Welcome, <?php echo htmlspecialchars($name); ?> (<?php echo htmlspecialchars($role); ?>)</span>
      <a href="Landing_page.html" class="btn btn-outline-light">Logout</a>
    </div>
  </nav>

  <div class="container mt-5">
    <?php echo $message; ?>
    <div class="row">
      
      <!-- Dashboard Section -->
      <div class="col-md-6 mb-4">
        <div class="card shadow">
          <div class="card-body">
            <h4 class="card-title mb-4 text-center"><?php echo ucfirst($role); ?> Dashboard</h4>
            <div class="row g-3">
              <?php if ($role === 'teacher'): ?>
                <div class="col-6">
                  <a href="manage_assignments.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100 hover-shadow border-0">
                      <div class="card-body">
                        <div class="fs-1 mb-2">📄</div>
                        <h6 class="card-title">Manage Assignments</h6>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-6">
                  <a href="submit_grades.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100 hover-shadow border-0">
                      <div class="card-body">
                        <div class="fs-1 mb-2">📊</div>
                        <h6 class="card-title">Submit Grades</h6>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-6">
                  <a href="grade_submission.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100 hover-shadow border-0">
                      <div class="card-body">
                        <div class="fs-1 mb-2">📚</div>
                        <h6 class="card-title">Student Submissions</h6>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-6">
                  <a href="post_announcement.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100 hover-shadow border-0">
                      <div class="card-body">
                        <div class="fs-1 mb-2">💬</div>
                        <h6 class="card-title">Post Announcements</h6>
                      </div>
                    </div>
                  </a>
                </div>
              <?php else: ?>
                <div class="col-6">
                  <a href="view_grades.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100 hover-shadow border-0">
                      <div class="card-body">
                        <div class="fs-1 mb-2">📈</div>
                        <h6 class="card-title">View Grades</h6>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-6">
                  <a href="student_down_assisignment.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100 hover-shadow border-0">
                      <div class="card-body">
                        <div class="fs-1 mb-2">📥</div>
                        <h6 class="card-title">Download Assignments</h6>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-6">
                  <a href="submit_assignment.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100 hover-shadow border-0">
                      <div class="card-body">
                        <div class="fs-1 mb-2">📤</div>
                        <h6 class="card-title">Submit Coursework</h6>
                      </div>
                    </div>
                  </a>
                </div>
                <div class="col-6">
                  <a href="join_discussions.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100 hover-shadow border-0">
                      <div class="card-body">
                        <div class="fs-1 mb-2">📝</div>
                        <h6 class="card-title">Join Discussions</h6>
                      </div>
                    </div>
                  </a>
                </div>
                
                <div class="col-6">
                  <a href="view_announcements.php" class="text-decoration-none">
                    <div class="card text-center shadow-sm h-100 hover-shadow border-0">
                      <div class="card-body">
                        <div class="fs-1 mb-2">📢</div>
                        <h6 class="card-title">View Announcements</h6>
                      </div>
                    </div>
                  </a>
                </div>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Update Info Form -->
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body">
            <h5 class="card-title">Update Your Info</h5>
            <form method="POST" class="mt-3">
              <?php if ($role === 'teacher'): ?>
                <div class="mb-3">
                  <label>Subject</label>
                  <input type="text" class="form-control" name="subject" required>
                </div>
                <div class="mb-3">
                  <label>Email</label>
                  <input type="email" class="form-control" name="email" required>
                </div>
                <div class="mb-3">
                  <label>Phone Number</label>
                  <input type="text" class="form-control" name="phone" required>
                </div>
              <?php else: ?>
                <div class="mb-3">
                  <label>Semester</label>
                  <input type="text" class="form-control" name="class" required>
                </div>
                <div class="mb-3">
                  <label>Enrollment Number</label>
                  <input type="text" class="form-control" name="roll" required>
                </div>
                <div class="mb-3">
                  <label>Email</label>
                  <input type="email" class="form-control" name="email" required>
                </div>
              <?php endif; ?>
              <button type="submit" class="btn btn-primary w-100">Update Info</button>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</body>
</html>

<?php $conn->close(); ?>
