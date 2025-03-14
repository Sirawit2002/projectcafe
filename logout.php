<?php
session_start();
session_destroy(); // ลบ session ทั้งหมด
header("Location: login.html"); // ส่งผู้ใช้กลับไปที่หน้า login
exit();
?>
