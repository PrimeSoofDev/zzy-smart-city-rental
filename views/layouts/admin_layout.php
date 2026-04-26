<?php
$role = $_SESSION['user_role'] ?? 'admin';
if ($role === 'staff') {
    include 'views/layouts/staff_layout_start.php';
} else {
    include 'views/layouts/admin_layout_start.php';
}

include $view;

if ($role === 'staff') {
    include 'views/layouts/staff_layout_end.php';
} else {
    include 'views/layouts/admin_layout_end.php';
}
