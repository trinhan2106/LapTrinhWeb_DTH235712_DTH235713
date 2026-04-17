<?php
/**
 * DANGXUAT.PHP – Xử lý đăng xuất (Đã chuyển ra Root)
 */
require_once "cauhinh.php";
session_start();

// 1. Xóa toàn bộ biến session
$_SESSION = array();

// 2. Nếu sử dụng cookie session, hãy xóa nó
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 3. Hủy session hoàn toàn
session_destroy();

// 4. Chuyển hướng về trang chủ công cộng qua BASE_URL
header("Location: " . BASE_URL . "/index.php");
exit();
