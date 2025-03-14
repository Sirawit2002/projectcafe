<?php
session_start();

// ตรวจสอบว่า session ของ username ถูกตั้งค่าหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // หากไม่มี session ให้ไปที่หน้าล็อกอิน
    exit();
}

$username = $_SESSION['username']; // เก็บชื่อผู้ใช้

// เชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "login_system";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลคาเฟ่และรูปภาพจากฐานข้อมูล
$sql = "SELECT cafes.id, cafes.name, cafes.description, 
        (SELECT image_url FROM cafe_images WHERE cafe_images.cafe_id = cafes.id LIMIT 1) AS image_url
        FROM cafes";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafes in Prachin Buri</title> <!-- Updated title -->
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* เปลี่ยนฟอนต์ให้เหมือนหน้า welcome */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #2E3B4E; /* Dark background color */
            color: #fff; /* White text color */
        }

        /* Navbar และเมนู */
        .navbar {
            background-color: #34495e; /* Dark navbar */
        }

        .navbar .nav-link {
            color: #fff !important; /* White text color */
            font-size: 16px;
            font-weight: bold;
            transition: color 0.3s;
        }

        .navbar .nav-link:hover {
            color: #FF7043 !important; /* Change on hover */
        }

        .navbar .dropdown-toggle {
            color: #fff !important;
        }

        .navbar .dropdown-menu {
            background-color:rgb(255, 255, 255);
        }

        .navbar .dropdown-item:hover {
            background-color: #FF7043;
        }
        .navbar {
    background-color: #34495e;
    padding: 15px 20px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
}
.navbar .nav-link, .navbar .dropdown-toggle {
    color: #fff !important;
    font-size: 18px;
    font-weight: 600;
}
.navbar .dropdown-menu {
    background-color: #fff;
}
.navbar .dropdown-item:hover {
    background-color: #FF7043;
}


        /* การปรับสีของปุ่ม Dropdown */
        .navbar .dropdown-menu .dropdown-item {
            color: #000 !important; /* สีตัวอักษรใน Dropdown เป็นสีดำ */
            background-color: #fff !important; /* สีพื้นหลังของ Dropdown เป็นสีขาว */
        }

        .navbar .dropdown-menu .dropdown-item:hover {
            background-color: #FF7043 !important; /* เมื่อ hover, สีพื้นหลังเป็นสีส้ม */
        }

        /* กรอบและการแสดงผลของการ์ด */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transition: transform 0.3s;
        }

        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
        }

        .card img {
            border-bottom: 5px solid #FF7043;
            height: 200px;
            object-fit: cover;
        }

        /* ปรับให้ฟอนต์ในการ์ดมีขนาดที่พอดี */
        .card-body p {
            font-size: 16px;
            text-align: center;
            color: #333;
        }

        footer {
            text-align: center;
            padding: 1rem;
            background-color: #34495e; /* Same dark color for footer */
            color: white;
            margin-top: 2rem;
        }

        footer a {
            color: #FF7043; /* Light orange for footer links */
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s;
        }

        footer a:hover {
            color: #FF5722; /* Hover effect for footer links */
        }

        footer .btn {
            background-color: #FF7043; /* เปลี่ยนสีพื้นหลังของปุ่ม */
            border: none;
            color: white; /* สีตัวอักษรในปุ่มเป็นสีขาว */
            font-size: 1.2rem;
            padding: 10px 20px;
            border-radius: 5px;
            transition: transform 0.3s;
        }

        footer .btn:hover {
            transform: scale(1.1);
            background-color: #FF5722; /* สีเมื่อ hover */
        }

    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="welcome.php">
                <img src="image/s3.png" alt="Logo" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="welcome.php">HOME</a></li>
                    <li class="nav-item"><a class="nav-link" href="cafes.php">CAFE</a></li>
                    <li class="nav-item"><a class="nav-link" href="video.php">VIDEO</a></li>
                    <!-- Dropdown สำหรับ Username และ Logout -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Welcome, <?php echo htmlspecialchars($username); ?>
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>CAFES</h1>
        <div class="row g-4">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $cafe_id = $row['id'];
                    $cafe_name = htmlspecialchars($row['name']);
                    $image_url = $row['image_url'] ? $row['image_url'] : 'image/default.jpg'; 
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <a href="cafe_details.php?cafe_id=<?php echo $cafe_id; ?>" class="text-decoration-none">
                            <div class="card">
                                <img src="<?php echo $image_url; ?>" alt="<?php echo $cafe_name; ?>" class="card-img-top">
                                <div class="card-body">
                                    <p class="card-text text-center"><?php echo $cafe_name; ?></p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                }
            } else {
                echo "<p>ยังไม่มีข้อมูลคาเฟ่</p>";
            }
            ?>
        </div>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>
