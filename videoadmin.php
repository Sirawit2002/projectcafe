<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "login_system";

$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT name, main_image, video_link FROM cafes WHERE video_link IS NOT NULL AND video_link != ''";
$result = $conn->query($sql);
$cafes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cafes[] = $row;
    }
}
$conn->close();

function getYouTubeID($url) {
    preg_match('/(?:youtu.be\/|youtube.com\/(?:.*v=|.*\/|.*embed\/|.*v%3D|.*%2Fv%2F|.*watch\?v=))([a-zA-Z0-9_-]{11})/', $url, $matches);
    return $matches[1] ?? '';
}
?>

<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #2E3B4E;
            color: #fff;
        }
        .navbar {
            background-color: #34495e;
        }
        .navbar .nav-link {
            color: #fff !important;
            font-weight: bold;
        }
        .navbar .nav-link:hover {
            color: #FF7043 !important;
        }
        .navbar .dropdown-menu {
            background-color: #fff;
        }
        .navbar .dropdown-item:hover {
            background-color: #FF7043;
        }
        .container {
            margin-top: 50px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-10px);
        }
        .card img {
            height: 200px;
            object-fit: cover;
        }
        footer {
            text-align: center;
            padding: 1rem;
            background-color: #34495e;
            color: white;
            margin-top: 2rem;
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
    <nav class="navbar navbar-expand-lg navbar-dark">
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
        <h1 class="text-center">Video Gallery</h1>
        <div class="row g-4">
            <?php foreach ($cafes as $cafe): ?>
                <div class="col-lg-4 col-md-6">
                    <a href="<?php echo htmlspecialchars($cafe['video_link']); ?>" target="_blank" class="text-decoration-none">
                        <div class="card">
                            <img src="<?php echo !empty($cafe['main_image']) ? htmlspecialchars($cafe['main_image']) : 'https://img.youtube.com/vi/' . getYouTubeID($cafe['video_link']) . '/0.jpg'; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($cafe['name']); ?>">
                            <div class="card-body">
                                <p class="card-text text-center"> <?php echo htmlspecialchars($cafe['name']); ?> </p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>