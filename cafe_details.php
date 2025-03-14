<?php
session_start();

// ตรวจสอบการเข้าถึงหน้าโดยไม่มีการ login
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // หากไม่มี session ให้ไปที่หน้าล็อคอิน
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

// รับ cafe_id จาก URL
if (isset($_GET['cafe_id'])) {
    $cafe_id = $_GET['cafe_id'];

    // ดึงข้อมูลคาเฟ่จากฐานข้อมูล
    $sql = "SELECT * FROM cafes WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $cafe_id); // ใช้ i สำหรับค่าที่เป็น integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $cafe = $result->fetch_assoc();
    } else {
        echo "<p>ไม่พบข้อมูลคาเฟ่</p>";
        exit();
    }

    // ดึงข้อมูล additional_images2
    $sql_images = "SELECT additional_images2 FROM cafes WHERE id = ?";
    $stmt_images = $conn->prepare($sql_images);
    $stmt_images->bind_param("i", $cafe_id);
    $stmt_images->execute();
    $result_images = $stmt_images->get_result();

    if ($result_images->num_rows > 0) {
        $images = $result_images->fetch_assoc();
        $additional_images = explode(',', $images['additional_images2']); // แยกรูปภาพด้วยคอมม่า
    }
} else {
    echo "<p>ไม่พบรหัสคาเฟ่</p>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>รายละเอียดคาเฟ่</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #2c3e50;
            color: white;
        }

        .navbar {
            background-color: #34495e;
        }

        .navbar .nav-link {
            color: white !important;
            font-weight: bold;
        }

        .navbar .nav-link:hover {
            color: #1abc9c !important;
        }

        .cafe-img {
            max-height: 400px;
            object-fit: cover;
            width: 100%;
            border-radius: 10px;
            display: block;
            margin: 0 auto;
        }

        .additional-images .img-container {
            border: 2px solid #1abc9c;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease-in-out;
        }

        .additional-images .img-container:hover {
            transform: scale(1.05);
        }

        .additional-images .img-container img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .cafe-details {
            background-color: #ecf0f1;
            color: black;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .cafe-title {
            font-size: 2rem;
            font-weight: 600;
        }

        .cafe-info p {
            font-size: 1.1rem;
        }

        /* Style for Modal */
        .modal-content {
            background-color: #2c3e50;
            color: white;
        }

        .modal-body img {
            width: 100%;
            height: auto;
        }

        .cafes-heading {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 3rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .big-text {
            font-size: 24px;
            font-weight: bold;
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

    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid">
            <a class="navbar-brand" href="welcome.php">
                <img src="image/s3.png" alt="Logo" height="50">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="welcome.php">HOME</a></li>
                    <li class="nav-item"><a class="nav-link" href="cafes.php">CAFE</a></li>
                    <li class="nav-item"><a class="nav-link" href="video.php">VIDEO</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
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

    <!-- Content Section -->
    <div class="container my-5">
        <div class="row">
            <div class="col-md-12 text-center position-relative">
                <img src="<?php echo !empty($cafe['main_image']) ? htmlspecialchars($cafe['main_image']) : 'image/default.jpg'; ?>"
                    class="cafe-img" alt="<?php echo htmlspecialchars($cafe['name']); ?>">

                <div class="col-md-12">
                    <div class="cafe-details">
                        <h2 class="cafe-title text-start"><?php echo htmlspecialchars($cafe['name']); ?></h2>
                        <div class="cafe-info text-start">
                            <p><strong>ที่อยู่:</strong> <?php echo htmlspecialchars($cafe['address'] ?? 'ไม่ระบุ'); ?></p>
                            <p><strong>คำอธิบาย:</strong> <?php echo nl2br(htmlspecialchars($cafe['description'] ?? 'ไม่ระบุ')); ?></p>
                            <p><strong>หมายเลขโทรศัพท์:</strong> <?php echo htmlspecialchars($cafe['phone'] ?? 'ไม่ระบุ'); ?></p>
                            <p><strong>เวลาทำการ:</strong> <?php echo htmlspecialchars($cafe['opening_time'] ?? 'ไม่ระบุ') . ' - ' . htmlspecialchars($cafe['closing_time'] ?? 'ไม่ระบุ'); ?></p>
                            <p><strong>ลิงค์วิดีโอ:</strong> <?php echo !empty($cafe['video_link']) ? '<a href="' . htmlspecialchars($cafe['video_link']) . '" target="_blank" class="btn btn-primary">ชมวิดีโอ</a>' : 'ไม่ระบุ'; ?></p>
                            <p><strong>สถานที่ตั้ง:</strong> <?php echo !empty($cafe['location']) ? '<a href="' . htmlspecialchars($cafe['location']) . '" target="_blank" class="btn btn-primary">ดูแผนที่</a>' : 'ไม่ระบุ'; ?></p>
                            <div class="text-start mt-3">
                               
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Images Section -->
                <div class="col-md-12 mt-4">
                    <div class="row additional-images">
                        <?php
                        if (!empty($additional_images)) {
                            $i = 0;
                            foreach ($additional_images as $image) {
                                if ($i % 3 == 0 && $i != 0) echo '</div><div class="row additional-images">'; // เปลี่ยนแถวทุก 3 รูป
                                echo '<div class="col-md-4 mb-4">
                                        <div class="img-container" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-img="' . htmlspecialchars($image) . '">
                                            <img src="' . htmlspecialchars($image) . '" alt="Additional Image" class="img-fluid">
                                        </div>
                                      </div>';
                                $i++;
                            }
                        } else {
                            echo '<p>ไม่พบรูปภาพเพิ่มเติม</p>';
                        }
                        ?>
                        <p class="big-text">รูปมุมถ่ายในคาเฟ่!!</p>
                        <?php
                        if (!empty($cafe['vit_cafe'])) {
                            $images = explode(',', $cafe['vit_cafe']);
                            echo '<div class="row">';
                            foreach ($images as $image) {
                                echo '<div class="col-md-4 mb-4">
                                        <div class="img-container" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-img="' . htmlspecialchars(trim($image)) . '">
                                            <img src="' . htmlspecialchars(trim($image)) . '" alt="Vit_Cafe Image" class="img-fluid" />
                                        </div>
                                      </div>';
                            }
                            echo '</div>';
                        } else {
                            echo '<p>ไม่มีภาพจาก Vit_Cafe</p>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Image Preview -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="" id="modalImage" alt="Image" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript to handle modal image change
        var imageContainers = document.querySelectorAll('.img-container');
        imageContainers.forEach(function(container) {
            container.addEventListener('click', function() {
                var imgSrc = this.getAttribute('data-bs-img');
                document.getElementById('modalImage').src = imgSrc;
            });
        });
    </script>
</body>

</html>
