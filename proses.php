<?php
require_once 'db.php';
require_once 'helpers.php';
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}

// Ambil JSON detail dari form
$detail_json = $_POST["detail"] ?? "";
$data        = json_decode($detail_json, true);

// nama project & aspek-aspek
$project_name = trim($data["project_name"] ?? "");
$aspects      = $data["aspects"]      ?? [];

// Hitung total kesalahan
$total_kesalahan = 0;
foreach ($aspects as $aspect) {
    if (!isset($aspect["subs"])) continue;
    foreach ($aspect["subs"] as $sub) {
        $total_kesalahan += intval($sub["mistake"]);
    }
}

// Hitung nilai akhir
$nilai_akhir = max(0, 90 - $total_kesalahan);

// Predikat sesuai rentang:
if ($nilai_akhir >= 86) {
    $predikat = "Istimewa";
} elseif ($nilai_akhir >= 78) {
    $predikat = "Sangat Baik";
} elseif ($nilai_akhir >= 65) {
    $predikat = "Baik";
} else {
    $predikat = "Cukup";
}

// Status Lulus jika nilai >= 65
$status = ($nilai_akhir >= 65) ? "LANJUT" : "ULANG";

// Cek apakah ini update (edit) atau insert baru
if (!empty($_POST["id"])) {
    $id = intval($_POST["id"]);
    $stmt = $conn->prepare("
        UPDATE evaluations
        SET project_name   = ?,
            detail         = ?,
            total_kesalahan= ?,
            nilai_akhir    = ?,
            predikat       = ?,
            status         = ?
        WHERE id = ?
    ");
    $stmt->bind_param(
        "ssiissi",
        $project_name,
        $detail_json,
        $total_kesalahan,
        $nilai_akhir,
        $predikat,
        $status,
        $id
    );
} else {
    $stmt = $conn->prepare("
        INSERT INTO evaluations
            (project_name, detail, total_kesalahan, nilai_akhir, predikat, status)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->bind_param(
        "ssiiss",
        $project_name,
        $detail_json,
        $total_kesalahan,
        $nilai_akhir,
        $predikat,
        $status
    );
}

$stmt->execute();
header("Location: hasil.php");
exit;
