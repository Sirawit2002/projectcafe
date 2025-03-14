<?php
session_start(); // เริ่มต้น session

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "login_system";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // ตรวจสอบในตาราง users ก่อน
    $sql = "SELECT * FROM users WHERE username = ?"; // ตรวจสอบในตาราง users
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    // ถ้าพบผู้ใช้ในตาราง users
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // ตรวจสอบรหัสผ่าน
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'user'; // บทบาท user

            // เปลี่ยนหน้าไปยังหน้าหลักของ user
            header("Location: welcome.php");
            exit();
        } else {
            echo "รหัสผ่านไม่ถูกต้อง!";
        }
    } else {
        // ถ้าไม่พบในตาราง users ให้ตรวจสอบในตาราง admins
        if ($username == "admin" && $password == "1234") {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = 'admin'; // บทบาท admin

            // เปลี่ยนหน้าไปยังหน้า admin_dashboard.php
            header("Location: admin_dashboard.php");
            exit();
        } else {
            // ถ้าไม่พบผู้ใช้ทั้งใน users และ admin
            echo "ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง!";
        }
    }
}

$conn->close();
?>

<!-- ฟอร์มล็อกอิน -->
<form method="POST" action="login.php">
    <label for="username">ชื่อผู้ใช้:</label>
    <input type="text" name="username" id="username" required>
    <br>
    <label for="password">รหัสผ่าน:</label>
    <input type="password" name="password" id="password" required>
    <br>
    <button type="submit">ล็อกอิน</button>
</form>
