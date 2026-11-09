<?php
session_start();
require_once __DIR__ . '/../includes/functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $user_id = $_SESSION['user_id'];

    deleteExpense($id, $user_id);
    header("Location: dashboard.php?deleted=1");
    exit;
} else {
    header("Location: dashboard.php?error=1");
    exit;
}
?>