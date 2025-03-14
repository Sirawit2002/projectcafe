<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // หากไม่มี session ให้ไปที่หน้าล็อกอิน
    exit();
}

// สร้าง CSRF token ถ้ายังไม่มี
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // สร้าง CSRF token แบบสุ่ม
}

$username = $_SESSION['username']; // เก็บชื่อผู้ใช้
?>

<!DOCTYPE html>
<html lang="th">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>สร้างคาเฟ่ใหม่</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #2c3e50; /* สีพื้นหลังที่มืดและทันสมัย */
      color: white; /* สีตัวอักษร */
    }
    nav {
      background-color: #34495e; /* สีพื้นหลังของ navbar */
    }
    .navbar-brand, .nav-link {
      color: white; /* สีตัวอักษรของ navbar */
    }
    .navbar-nav .nav-link:hover {
      color: #1abc9c; /* สีตัวอักษรเวลา hover */
    }
    .container {
      background-color: #ecf0f1; /* พื้นหลังของฟอร์ม */
      border-radius: 8px; /* ขอบมนให้ดูนุ่มนวล */
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* เงา */
      padding: 20px;
    }
    .form-label {
      color: #34495e; /* สีของข้อความ label */
    }
    .form-control {
      border-radius: 4px; /* ขอบมน */
    }
    .btn-primary {
      background-color: #007bff; /* สีของปุ่ม */
      border-color: #007bff; /* สีของขอบปุ่ม */
    }
    .btn-primary:hover {
      background-color: #0056b3; /* สีของปุ่มเมื่อ hover */
      border-color: #004085;
    }
    .form-text {
      font-size: 0.875rem;
      color: #6c757d;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="welcome.php">Cafes in Prachin Buri</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="welcome.php">หน้าแรก</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="cafes.php">คาเฟ่</a>
          </li>
          <li class="nav-item">
            <span class="nav-link">ยินดีต้อนรับ, <?php echo htmlspecialchars($username); ?>!</span>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Form Section -->
  <div class="container my-5">
    <h2>สร้างคาเฟ่ใหม่</h2>
    <form action="process_cafe.php" method="POST" enctype="multipart/form-data">
      <!-- CSRF Token -->
      <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

    
      <div class="mb-3">
        <label for="cafeImages" class="form-label">ภาพหลักของคาเฟ่</label>   <!-- ควรเลือกได้แค่รูปเดียว -->
        <input type="file" class="form-control" id="cafeImages" name="cafe_images[]" accept="image/*" multiple required>
        <small class="form-text text-muted">เลือกภาพหลักที่จะแสดงในหน้า</small>
      </div>

      <!-- เลือกได้หลายรูป 2 -5 -->
      <div class="mb-3">
        <label for="mainCafeImage" class="form-label">ภาพรวมของคาเฟ่</label>
        <input type="file" class="form-control" id="mainCafeImage" name="main_cafe_image" accept="image/*" required>
        <small class="form-text text-muted">คุณสามารถเลือกได้หลายภาพ ()</small>
      </div>
      <!-- ภาพเพิ่มเติม 2 -->
      <div class="mb-3">
        <label for="additionalImages2" class="form-label">ภาพเพิ่มเติมที่สอง</label> <!-- เพิ่มภาพเพิ่มเติมที่สอง -->
        <input type="file" class="form-control" id="additionalImages2" name="additional_images2[]" accept="image/*" multiple>
        <small class="form-text text-muted">คุณสามารถเลือกภาพเพิ่มเติมที่สองได้</small>

    <div class="mb-3">
      <label for="vitcafe" class="form-label">รูปมุมคาเฟ่</label>
      <input type="file" class="form-control" id="vitcafe" name="vit_cafe[]" accept="image/*" multiple>
      <small class="form-text text-muted">อัปโหลดรูปภาพหลายรูปได้</small>
  </div>





      <!-- ฟอร์มสำหรับอัปโหลดหลายๆ รูป -->
   

      <!-- ฟิลด์อื่นๆ เช่น ชื่อคาเฟ่ -->
      <div class="mb-3">
        <label for="cafeName" class="form-label">ชื่อคาเฟ่</label>
        <input type="text" class="form-control" id="cafeName" name="cafe_name" required>
      </div>

      <!-- ฟิลด์สำหรับที่อยู่ -->
      <div class="mb-3">
        <label for="cafeAddress" class="form-label">ที่อยู่ของคาเฟ่</label>
        <input type="text" class="form-control" id="cafeAddress" name="cafe_address" required>
      </div>

      <!-- ฟิลด์สำหรับหมายเลขโทรศัพท์ -->
      <div class="mb-3">
        <label for="cafePhone" class="form-label">หมายเลขติดต่อ</label>
        <input type="text" class="form-control" id="cafePhone" name="cafe_phone" required>
      </div>

      <!-- ฟิลด์คำอธิบาย -->
      <div class="mb-3">
        <label for="cafeDescription" class="form-label">คำอธิบายคาเฟ่</label>
        <textarea class="form-control" id="cafeDescription" name="cafe_description" rows="4" placeholder="กรอกคำอธิบายของคาเฟ่ เช่น เมนูพิเศษ หรือบรรยากาศของร้าน" required></textarea>
      </div>

      <!-- ฟิลด์สำหรับ URL วิดีโอ -->
      <div class="mb-3">
        <label for="cafeVideo" class="form-label">ลิงก์วิดีโอ (เช่น YouTube)</label>
        <input type="url" class="form-control" id="cafeVideo" name="cafe_video" placeholder="เช่น https://www.youtube.com/watch?v=xyz" required>
      </div>

      <!-- ฟิลด์พิกัด GPS หรือ URL แผนที่ -->
      <div class="mb-3">
        <label for="cafeLocation" class="form-label">สถานที่ตั้ง (พิกัด GPS หรือ URL แผนที่)</label>
        <input type="text" class="form-control" id="cafeLocation" name="cafe_location" placeholder="เช่น https://goo.gl/maps/xyz" required>
      </div>

      <!-- ฟิลด์เวลาเปิดปิด -->
      <div class="mb-3">
        <label for="cafeOpeningTime" class="form-label">เวลาเปิด</label>
        <input type="time" class="form-control" id="cafeOpeningTime" name="opening_time" required>
      </div>

      <div class="mb-3">
        <label for="cafeClosingTime" class="form-label">เวลาเปิดปิด</label>
        <input type="time" class="form-control" id="cafeClosingTime" name="closing_time" required>
      </div>

      <!-- ปุ่มยืนยัน -->
      <div class="mb-3">
        <button type="submit" class="btn btn-primary">ยืนยัน</button>
      </div>
    </form>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
