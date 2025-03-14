<?php
// ตัวอย่างเมื่อคุณเพิ่มผู้ใช้หรือ admin ใหม่
$username = 'admin';  // ชื่อผู้ใช้
$password = '1234';    // รหัสผ่าน

// แฮชรหัสผ่าน
$hashed_password = password_hash($password, PASSWORD_DEFAULT); 

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "login_system";
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// เพิ่มข้อมูลในตาราง admins
$sql = "INSERT INTO admin (username, password) VALUES (?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $username, $hashed_password);
$stmt->execute();

echo "เพิ่มข้อมูล admin สำเร็จ!";

$conn->close();
?>
