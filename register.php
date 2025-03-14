<?php
// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "root";  // ชื่อผู้ใช้ฐานข้อมูล
$password = "";      // รหัสผ่านฐานข้อมูล
$dbname = "login_system";  // ชื่อฐานข้อมูล

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ตรวจสอบว่าฟอร์มถูกส่งมาหรือไม่
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = $_POST['username'];
    $new_password = $_POST['password'];

    // ตรวจสอบว่าผู้ใช้กรอกข้อมูลหรือไม่
    if (empty($new_username) || empty($new_password)) {
        echo "กรุณากรอกข้อมูลให้ครบถ้วน";
    } else {
        // เข้ารหัสรหัสผ่านด้วย password_hash
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        // คำสั่ง SQL เพื่อเพิ่มข้อมูลผู้ใช้ใหม่
        $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

        // เตรียมคำสั่ง SQL
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $new_username, $hashed_password); // ใช้ bind_param เพื่อป้องกัน SQL Injection
            if ($stmt->execute()) {
                echo "สมัครสมาชิกสำเร็จ!";
            } else {
                echo "เกิดข้อผิดพลาดในการสมัครสมาชิก: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>สมัครสมาชิก</title>
    <style>
        /* ทำให้หน้าเว็บเต็มความกว้างของหน้าจอ */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        /* กำหนดความกว้างและการตั้งกลางฟอร์ม */
        .register-container {
            background-color: #fff;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            width: 100%;
            max-width: 400px;
        }

        /* กำหนดความสวยงามของหัวข้อ */
        h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #333;
        }

        /* กำหนดสไตล์ให้กับป้ายชื่อ (label) */
        label {
            display: block;
            margin-bottom: 10px;
            font-size: 16px;
            color: #555;
        }

        /* สไตล์ของ input */
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0 20px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }

        /* ปรับสไตล์ให้กับปุ่มสมัคร */
        button {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        /* เปลี่ยนสีปุ่มเมื่อ hover */
        button:hover {
            background-color: #45a049;
        }

        /* สไตล์ข้อความแจ้งเตือน */
        .message {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
            color: #333;
        }

        .message a {
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>สมัครสมาชิก</h2>

        <!-- ฟอร์มสมัครสมาชิก -->
        <form action="register.php" method="POST">
            <label for="username">ชื่อผู้ใช้:</label>
            <input type="text" id="username" name="username" required><br>

            <label for="password">รหัสผ่าน:</label>
            <input type="password" id="password" name="password" required><br>

            <button type="submit">สมัครสมาชิก</button>
        </form>

        <!-- ข้อความแจ้งเตือน -->
        <div class="message">
            <p>มีบัญชีแล้ว? <a href="login.html">เข้าสู่ระบบ</a></p>
        </div>
    </div>
</body>
</html>