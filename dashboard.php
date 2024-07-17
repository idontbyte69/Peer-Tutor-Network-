<?php
session_start();
$user_id = $_SESSION['user_id']; // Assuming you store user ID in the session after login

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "peer_tutoring_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $birthdate = $_POST['birthdate'];
    $address = $_POST['address'];
    $education = $_POST['education'];

    // Handle profile picture upload
    if (!empty($_FILES['profile_pic']['name'])) {
        $profile_pic = $_FILES['profile_pic'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($profile_pic["name"]);
        move_uploaded_file($profile_pic["tmp_name"], $target_file);
        // Update query to include profile picture
        $sql = "UPDATE users SET name=?, email=?, birthdate=?, address=?, education=?, profile_pic=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $name, $email, $birthdate, $address, $education, $target_file, $user_id);
    } else {
        // Update query without profile picture
        $sql = "UPDATE users SET name=?, email=?, birthdate=?, address=?, education=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $email, $birthdate, $address, $education, $user_id);
    }

    if ($stmt->execute()) {
        echo "Profile updated successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

// Fetch user information
$sql = "SELECT name, email, birthdate, address, education, profile_pic FROM users WHERE id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($name, $email, $birthdate, $address, $education, $profile_pic);
$stmt->fetch();
$stmt->close();

// Fetch courses/subjects
$sql = "SELECT course_name FROM courses WHERE user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$courses = [];
while ($row = $result->fetch_assoc()) {
    $courses[] = $row['course_name'];
}
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <h2 class="text-center">User Dashboard</h2>
    <form action="dashboard.php" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label for="profile-pic">Profile Picture</label>
            <?php if ($profile_pic): ?>
                <img src="<?php echo $profile_pic; ?>" alt="Profile Picture" class="img-thumbnail" width="150">
            <?php endif; ?>
            <input type="file" class="form-control" id="profile-pic" name="profile_pic">
        </div>
        <div class="form-group">
            <label for="name">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo $name; ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email address</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" required>
        </div>
        <div class="form-group">
            <label for="birthdate">Birthdate</label>
            <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo $birthdate; ?>" required>
        </div>
        <div class="form-group">
            <label for="address">Address</label>
            <input type="text" class="form-control" id="address" name="address" value="<?php echo $address; ?>" required>
        </div>
        <div class="form-group">
            <label for="education">Educational Information</label>
            <textarea class="form-control" id="education" name="education" required><?php echo $education; ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
    <h3 class="text-center mt-5">Courses/Subjects Helped With</h3>
    <div id="courses-list">
        <?php if ($courses): ?>
            <ul class="list-group">
                <?php foreach ($courses as $course): ?>
                    <li class="list-group-item"><?php echo htmlspecialchars($course); ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No courses/subjects found.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
