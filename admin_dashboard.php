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

// ดึงข้อมูลเฉพาะคาเฟ่ที่มี id 147, 150, 149
$sql = "SELECT cafes.id, cafes.name, 
        (SELECT image_url FROM cafe_images WHERE cafe_images.cafe_id = cafes.id LIMIT 1) AS image_url
        FROM cafes 
        WHERE cafes.id IN (147, 150, 151)";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cafes in Prachin Buri</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #2E3B4E;
            color: #fff;
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
        .hero {
            background: url('image/hero-bg.jpg') no-repeat center center/cover;
            color: white;
            padding: 150px 0;
            text-align: center;
            position: relative;
            animation: fadeIn 2s;
        }
        .hero h1 {
            font-size: 3.5rem;
            font-weight: bold;
            text-shadow: 2px 2px 10px rgba(0, 0, 0, 0.5);
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.4);
        }
        .card img {
            border-bottom: 5px solid #FF7043;
            height: 220px;
            object-fit: cover;
        }
        footer {
            text-align: center;
            padding: 1rem;
            background-color: #34495e;
            color: white;
            margin-top: 2rem;
            font-size: 18px;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="admin_dashboard.php">
                <img src="image/s3.png" alt="Logo" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_dashboard.php">HOME</a></li>
                    <li class="nav-item"><a class="nav-link" href="cafesadmin.php">CAFE</a></li>
                    <li class="nav-item"><a class="nav-link" href="videoadmin.php">VIDEO</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Welcome, <?php echo htmlspecialchars($username); ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <section class="hero animate__animated animate__fadeIn">
        <div class="container">
            <h1>Welcome to TOP CAFES</h1>
            <p>Discover the best cafes around you!</p>
        </div>
    </section>
    <div class="container mt-4">
        <div class="row g-4">
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $cafe_id = $row['id'];
                    $cafe_name = htmlspecialchars($row['name']);
                    $image_url = $row['image_url'] ? $row['image_url'] : 'image/default.jpg'; 
                    ?>
                    <div class="col-lg-4 col-md-6">
                        <a href="cafeadmin_details.php?cafe_id=<?php echo $cafe_id; ?>" class="text-decoration-none">
                            <div class="card animate__animated animate__zoomIn">
                                <img src="<?php echo $image_url; ?>" alt="<?php echo $cafe_name; ?>" class="card-img-top">
                                <div class="card-body">
                                    <p class="card-text text-center"> <?php echo $cafe_name; ?> </p>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php
                }
            } else {
                echo "<p class='text-center'>ยังไม่มีข้อมูลคาเฟ่</p>";
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
