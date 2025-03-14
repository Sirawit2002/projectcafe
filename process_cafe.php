<?php
session_start();

// ตรวจสอบ CSRF Token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("Token ไม่ถูกต้อง");
}

// ตรวจสอบว่า session ของ username ถูกตั้งค่าหรือไม่
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // หากไม่มี session ให้ไปที่หน้าล็อกอิน
    exit();
}

$username = $_SESSION['username']; // เก็บชื่อผู้ใช้
$is_admin = ($_SESSION['role'] === 'admin'); // ตรวจสอบว่าเป็นแอดมินหรือไม่

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

// ตรวจสอบการส่งข้อมูลผ่านฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // รับค่าจากฟอร์มและป้องกัน SQL Injection
    $name = $conn->real_escape_string($_POST['cafe_name']);
    $address = $conn->real_escape_string($_POST['cafe_address']);
    $description = $conn->real_escape_string($_POST['cafe_description']);
    $phone = $conn->real_escape_string($_POST['cafe_phone']);
    $video_link = $conn->real_escape_string($_POST['cafe_video']); // รับลิงก์วิดีโอ
    $location = $conn->real_escape_string($_POST['cafe_location']); // รับพิกัดหรือ URL แผนที่

    // รับค่าเวลาเปิดและปิดจากฟอร์ม
    $opening_time = $conn->real_escape_string($_POST['opening_time']);
    $closing_time = $conn->real_escape_string($_POST['closing_time']);

    // เพิ่มข้อมูลคาเฟ่ลงในตาราง `cafes`
    $sql = "INSERT INTO cafes (name, address, description, phone, video_link, location, opening_time, closing_time) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $name, $address, $description, $phone, $video_link, $location, $opening_time, $closing_time);

    if ($stmt->execute()) {
        // รับ ID ของคาเฟ่ที่เพิ่ม
        $cafe_id = $stmt->insert_id;

        // การอัปโหลดภาพหลัก (Main Image)
        if (isset($_FILES['main_cafe_image']) && $_FILES['main_cafe_image']['error'] === UPLOAD_ERR_OK) {
            $main_image_name = $_FILES['main_cafe_image']['name'];
            $main_image_tmp_name = $_FILES['main_cafe_image']['tmp_name'];
            $main_image_size = $_FILES['main_cafe_image']['size'];
            $main_image_error = $_FILES['main_cafe_image']['error'];
            $main_file_ext = strtolower(pathinfo($main_image_name, PATHINFO_EXTENSION));
            
            // ตรวจสอบประเภทไฟล์
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($main_file_ext, $allowed_ext)) {
                // ตรวจสอบขนาดไฟล์
                if ($main_image_size <= 2097152) {
                    $unique_main_image = uniqid() . "." . $main_file_ext;
                    $target_main_image = "uploads/" . $unique_main_image;

                    if (move_uploaded_file($main_image_tmp_name, $target_main_image)) {
                        // เพิ่ม URL รูปภาพหลักลงในฐานข้อมูล
                        $sql_main_image = "UPDATE cafes SET main_image = ? WHERE id = ?";
                        $stmt_main_image = $conn->prepare($sql_main_image);
                        $stmt_main_image->bind_param("si", $target_main_image, $cafe_id);
                        $stmt_main_image->execute();
                    } else {
                        echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์หลัก";
                    }
                } else {
                    echo "ไฟล์ใหญ่เกินไป! ขนาดไฟล์ต้องไม่เกิน 2MB.";
                }
            } else {
                echo "ประเภทไฟล์ไม่ถูกต้อง! รองรับไฟล์ .jpg, .jpeg, .png, .gif เท่านั้น.";
            }
        }

        // การอัปโหลดหลายรูปภาพ (สำหรับบรรยากาศ)
        if (isset($_FILES['cafe_images']) && count($_FILES['cafe_images']['name']) > 0) {
            $image_urls = [];
            $target_dir = "uploads/";

            // สร้างโฟลเดอร์หากยังไม่มี
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            // กำหนดนามสกุลที่อนุญาต
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

            foreach ($_FILES['cafe_images']['name'] as $key => $image_name) {
                $image_tmp_name = $_FILES['cafe_images']['tmp_name'][$key];
                $image_size = $_FILES['cafe_images']['size'][$key];
                $image_error = $_FILES['cafe_images']['error'][$key];
                $file_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

                if (in_array($file_ext, $allowed_ext)) {
                    if ($image_size <= 2097152) {
                        if ($image_error === UPLOAD_ERR_OK) {
                            $unique_image_name = uniqid() . "." . $file_ext;
                            $target_image = $target_dir . $unique_image_name;

                            if (move_uploaded_file($image_tmp_name, $target_image)) {
                                $image_urls[] = $target_image;
                            } else {
                                echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์รูปภาพ.";
                            }
                        }
                    } else {
                        echo "ไฟล์รูปภาพที่เลือกใหญ่เกินไป! ขนาดไฟล์ต้องไม่เกิน 2MB.";
                    }
                } else {
                    echo "ประเภทไฟล์ไม่ถูกต้อง! รองรับไฟล์ .jpg, .jpeg, .png, .gif เท่านั้น.";
                }
            }

            // เพิ่ม URL ของภาพลงในฐานข้อมูล
            foreach ($image_urls as $url) {
                $sql_images = "INSERT INTO cafe_images (cafe_id, image_url) VALUES (?, ?)";
                $stmt_images = $conn->prepare($sql_images);
                $stmt_images->bind_param("is", $cafe_id, $url);
                $stmt_images->execute();
            }
        }

        // การอัปโหลดภาพเพิ่มเติม 2
        if (isset($_FILES['additional_images2']) && count($_FILES['additional_images2']['name']) > 0) {
            $additional_image_urls2 = [];
            $target_dir = "uploads/";

            // สร้างโฟลเดอร์หากยังไม่มี
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            // กำหนดนามสกุลที่อนุญาต
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

            foreach ($_FILES['additional_images2']['name'] as $key => $image_name) {
                $image_tmp_name = $_FILES['additional_images2']['tmp_name'][$key];
                $image_size = $_FILES['additional_images2']['size'][$key];
                $image_error = $_FILES['additional_images2']['error'][$key];
                $file_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

                if (in_array($file_ext, $allowed_ext)) {
                    if ($image_size <= 2097152) {
                        if ($image_error === UPLOAD_ERR_OK) {
                            $unique_image_name = uniqid() . "." . $file_ext;
                            $target_image = $target_dir . $unique_image_name;

                            if (move_uploaded_file($image_tmp_name, $target_image)) {
                                $additional_image_urls2[] = $target_image;
                            } else {
                                echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์รูปภาพ.";
                            }
                        }
                    } else {
                        echo "ไฟล์รูปภาพที่เลือกใหญ่เกินไป! ขนาดไฟล์ต้องไม่เกิน 2MB.";
                    }
                } else {
                    echo "ประเภทไฟล์ไม่ถูกต้อง! รองรับไฟล์ .jpg, .jpeg, .png, .gif เท่านั้น.";
                }
            }

            // เพิ่ม URL ของภาพเพิ่มเติม 2 ลงในฐานข้อมูล
            $additional_images2_str = implode(',', $additional_image_urls2);  // แปลงเป็นข้อความ
            $sql_images = "UPDATE cafes SET additional_images2 = ? WHERE id = ?";
            $stmt_images = $conn->prepare($sql_images);
            $stmt_images->bind_param("si", $additional_images2_str, $cafe_id);
            $stmt_images->execute();
        }

        // การอัปโหลดรูปภาพสำหรับ Vit Cafe
        if (isset($_FILES['vit_cafe']) && count($_FILES['vit_cafe']['name']) > 0) {
            $vit_cafe_urls = [];
            $target_dir = "uploads/";

            // สร้างโฟลเดอร์หากยังไม่มี
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            // กำหนดนามสกุลที่อนุญาต
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

            foreach ($_FILES['vit_cafe']['name'] as $key => $image_name) {
                $image_tmp_name = $_FILES['vit_cafe']['tmp_name'][$key];
                $image_size = $_FILES['vit_cafe']['size'][$key];
                $image_error = $_FILES['vit_cafe']['error'][$key];
                $file_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));

                if (in_array($file_ext, $allowed_ext)) {
                    if ($image_size <= 2097152) {
                        if ($image_error === UPLOAD_ERR_OK) {
                            $unique_image_name = uniqid() . "." . $file_ext;
                            $target_image = $target_dir . $unique_image_name;

                            if (move_uploaded_file($image_tmp_name, $target_image)) {
                                $vit_cafe_urls[] = $target_image;
                            } else {
                                echo "เกิดข้อผิดพลาดในการอัปโหลดไฟล์รูปภาพ.";
                            }
                        }
                    } else {
                        echo "ไฟล์รูปภาพที่เลือกใหญ่เกินไป! ขนาดไฟล์ต้องไม่เกิน 2MB.";
                    }
                } else {
                    echo "ประเภทไฟล์ไม่ถูกต้อง! รองรับไฟล์ .jpg, .jpeg, .png, .gif เท่านั้น.";
                }
            }

            // เพิ่ม URL ของรูปภาพสำหรับ Vit Cafe ลงในฐานข้อมูล
            $vit_cafe_str = implode(',', $vit_cafe_urls);  // แปลงเป็นข้อความ
            $sql_images = "UPDATE cafes SET vit_cafe = ? WHERE id = ?";
            $stmt_images = $conn->prepare($sql_images);
            $stmt_images->bind_param("si", $vit_cafe_str, $cafe_id);
            $stmt_images->execute();
        }

        // แสดงข้อความสำเร็จ พร้อมปุ่มกดปิด
        echo "<script>
                alert('กรอกข้อมูลสำเร็จ!');
                window.location.href = 'cafesadmin.php'; // เปลี่ยนเส้นทางไปที่ cafes.php
              </script>";
        exit();
    } else {
        echo "เกิดข้อผิดพลาด: " . $stmt->error;
    }
} else {
    echo "ข้อมูลไม่ถูกต้อง!";
}

$conn->close();
?>
