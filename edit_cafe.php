<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "login_system";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['cafe_id'])) {
    die("ไม่พบรหัสคาเฟ่");
}

$cafe_id = $_GET['cafe_id'];
$sql = "SELECT * FROM cafes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cafe_id);
$stmt->execute();
$result = $stmt->get_result();
$cafe = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $address = $_POST['address'];
    $description = $_POST['description'];
    $phone = $_POST['phone'];
    $opening_time = $_POST['opening_time'];
    $closing_time = $_POST['closing_time'];
    $video_link = $_POST['video_link'];
    $location = $_POST['location'];
    
    // Handle Image Upload
    if (!empty($_FILES['main_image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['main_image']['name']);
        move_uploaded_file($_FILES['main_image']['tmp_name'], $target_file);
    } else {
        $target_file = $cafe['main_image'];
    }

    $update_sql = "UPDATE cafes SET name=?, address=?, description=?, phone=?, opening_time=?, closing_time=?, video_link=?, location=?, main_image=? WHERE id=?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssssssssi", $name, $address, $description, $phone, $opening_time, $closing_time, $video_link, $location, $target_file, $cafe_id);

    if ($update_stmt->execute()) {
        // ข้อมูลอัปเดตสำเร็จ ให้ redirect ไปที่ cafeadmin_details.php
        echo "<script>alert('ข้อมูลคาเฟ่ได้รับการอัปเดตเรียบร้อยแล้ว'); window.location.href = 'cafeadmin_details.php?cafe_id=" . $cafe_id . "';</script>";
    } else {
        echo "เกิดข้อผิดพลาดในการอัปเดตข้อมูล";
    }
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แก้ไขคาเฟ่</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #34495e; /* สีพื้นหลังของหน้าเว็บ */
            color: white; /* สีข้อความ */
        }

        .container {
            background-color: #ecf0f1; /* สีพื้นหลังของฟอร์ม */
            border-radius: 8px; /* ขอบมน */
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* เงา */
        }

        .form-label {
            color: #34495e; /* สีของข้อความ label */
        }

        .form-control {
            border-radius: 4px; /* ขอบมน */
            border: 1px solid #bdc3c7; /* เส้นขอบสีอ่อน */
        }

        .form-control:focus {
            border-color: #1abc9c; /* สีของขอบตอนเลือก */
        }

        .btn-success {
            background-color: #1abc9c; /* สีปุ่ม */
            border-color: #16a085; /* ขอบปุ่ม */
        }

        .btn-success:hover {
            background-color: #16a085; /* สีปุ่มเมื่อ hover */
            border-color: #1abc9c;
        }

        .btn-secondary {
            background-color: #95a5a6; /* สีของปุ่มย้อนกลับ */
            border-color: #7f8c8d;
        }

        .btn-secondary:hover {
            background-color: #7f8c8d; /* สีของปุ่มเมื่อ hover */
            border-color: #95a5a6;
        }

        img {
            border-radius: 4px; /* ขอบมนสำหรับรูป */
            margin-top: 10px;
        }

        h2 {
            color: #34495e; /* สีของหัวข้อ */
        }

        .current-image-text {
            color: #34495e; /* สีของข้อความ "รูปภาพปัจจุบัน" */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>แก้ไขข้อมูลคาเฟ่</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label class="form-label">ชื่อคาเฟ่</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($cafe['name']); ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">ที่อยู่</label>
            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($cafe['address']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">คำอธิบาย</label>
            <textarea name="description" class="form-control"><?php echo htmlspecialchars($cafe['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">เบอร์โทรศัพท์</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($cafe['phone']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">เวลาเปิด</label>
            <input type="time" name="opening_time" class="form-control" value="<?php echo htmlspecialchars($cafe['opening_time']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">เวลาปิด</label>
            <input type="time" name="closing_time" class="form-control" value="<?php echo htmlspecialchars($cafe['closing_time']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">ลิงค์วิดีโอ</label>
            <input type="url" name="video_link" class="form-control" value="<?php echo htmlspecialchars($cafe['video_link']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">ลิงค์แผนที่</label>
            <input type="url" name="location" class="form-control" value="<?php echo htmlspecialchars($cafe['location']); ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">อัปโหลดรูปภาพ</label>
            <input type="file" name="main_image" class="form-control">
            <p class="current-image-text">รูปภาพปัจจุบัน:</p>
            <img src="<?php echo htmlspecialchars($cafe['main_image']); ?>" alt="<?php echo htmlspecialchars($cafe['name']); ?>" style="max-width: 200px;">
        </div>
        <button type="submit" class="btn btn-success">บันทึกการเปลี่ยนแปลง</button>
        <a href="cafeadmin_details.php?cafe_id=<?php echo $cafe_id; ?>" class="btn btn-secondary">กลับไปยังรายละเอียด</a>
    </form>
</div>
</body>
</html>
