<?php
require_once 'db.php';
session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$rows = $conn->query("SELECT * FROM evaluations ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Website Penilaian</title>
  <style>
    /* Gunakan background gambar dari folder assets */
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: url('assets/background.jpg') no-repeat center center fixed;
      background-size: cover;
    }

    .container {
      max-width: 900px;
      margin: 24px auto;
      background: rgba(255,255,255,0.9);
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0,0,0,.1);
    }

    h2 {
      text-align: center;
      margin-top: 0;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 12px;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 6px;
      text-align: center;
    }
    th {
      background: #f2f2f2;
    }
    table tr:hover {
      background: #f9f9f9;
    }

    /* Button styles */
    .btn {
      display: inline-block;
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      background: #2196f3;
      color: #fff;
      text-decoration: none;
      margin: 2px;
      transition: all 0.2s ease;
    }
    .btn:hover {
      background: #1976d2;
      transform: translateY(-1px);
    }
    .btn-danger {
      background: #e53935;
    }
    .btn-danger:hover {
      background: #d32f2f;
      transform: translateY(-1px);
    }

    /* Navigation links */
    nav a {
      text-decoration: none;
      color: #fff;
      padding: 8px 14px;
      border-radius: 4px;
      transition: background 0.2s ease, opacity 0.2s ease;
    }
    nav a:hover {
      opacity: 0.8;
    }
  </style>
</head>
<body>
<?php
$current = basename($_SERVER['PHP_SELF']);
function navLink(string $file, string $label, string $current): string {
    $active = ($file === $current);
    $bg     = $active ? '#4CAF50' : 'transparent';
    return "<a href=\"$file\" style=\"background:$bg;color:#fff;\">$label</a>";
}
?>
<nav style="background:#333;padding:10px 18px;display:flex;align-items:center;justify-content:space-between;">
  <div style="display:flex;gap:12px">
    <?= navLink('index.php', 'Penilaian', $current); ?>
    <?= navLink('hasil.php', 'Hasil Penilaian', $current); ?>
  </div>
  <a href="logout.php" class="btn btn-danger">Logout</a>
</nav>

<div class="container">
  <h2>Hasil Penilaian Project</h2>

  <?php if (!$rows->num_rows): ?>
    <p style="text-align:center;">Belum ada data penilaian.</p>
  <?php else: ?>
    <table>
      <tr>
        <th style="text-align:left;">Nama Project</th>
        <th>Total Nilai</th>
        <th>Predikat</th>
        <th>Status</th>
        <th>Aksi</th>
      </tr>
      <?php while ($r = $rows->fetch_assoc()): ?>
        <tr>
          <td style="text-align:left;"><?= htmlspecialchars($r['project_name']); ?></td>
          <td><?= $r['nilai_akhir']; ?></td>
          <td><?= $r['predikat']; ?></td>
          <td><?= $r['status']; ?></td>
          <td>
            <a class="btn" href="index.php?id=<?= $r['id']; ?>">Edit</a>
            <a class="btn btn-danger"
               href="delete.php?id=<?= $r['id']; ?>"
               onclick="return confirm('Hapus data?')">
               Delete
            </a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php endif; ?>
</div>
</body>
</html>
