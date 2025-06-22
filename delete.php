<?php
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';
$id = intval($_GET['id'] ?? 0);
$conn->query("DELETE FROM evaluations WHERE id=$id");
header("Location: hasil.php");
exit;
?>
