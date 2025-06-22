<?php
require_once 'db.php';
require_once 'helpers.php';

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

/* -------- mode tambah / edit -------- */
$edit = isset($_GET['id']);
$data = ['project_name'=>'','aspects'=>[]];

if ($edit) {
    $id  = (int) $_GET['id'];
    $row = $conn->query("SELECT project_name, detail FROM evaluations WHERE id=$id")
                ->fetch_assoc();
    $data = json_decode($row['detail'], true);
    $data['project_name'] = $row['project_name'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Website Penilaian</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      font-family: Arial, sans-serif;
      background: url('assets/background.jpg') no-repeat center center fixed;
      background-size: cover;
    }

    .container {
      max-width: 1100px;
      margin: 24px auto;
      background: rgba(255,255,255,0.9);
      padding: 25px;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0,0,0,.1);
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
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
    input:focus {
      outline: none;
      border-color: #2196f3;
      box-shadow: 0 0 3px rgba(33, 150, 243, .5);
    }

    /* Button styles */
    .btn {
      padding: 6px 12px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      color: #fff;
      margin: 3px;
      transition: all 0.2s ease;
    }
    .btn-primary { background: #2196f3; }
    .btn-primary:hover { background: #1976d2; transform: translateY(-1px); }
    .btn-success { background: #4CAF50; }
    .btn-success:hover { background: #43a047; transform: translateY(-1px); }
    .btn-danger { background: #e53935; }
    .btn-danger:hover { background: #d32f2f; transform: translateY(-1px); }
    .btn-warning { background: #ff9800; }
    .btn-warning:hover { background: #fb8c00; transform: translateY(-1px); }

    /* Nav links */
    nav a {
      text-decoration: none;
      transition: opacity 0.2s ease, background 0.2s ease;
      color: #fff;
      padding: 8px 14px;
      border-radius: 4px;
    }
    nav a:hover { opacity: 0.8; }
    .num { width: 70px; text-align: center; }
  </style>
</head>
<body>
<?php
$current = basename($_SERVER['PHP_SELF']);
function navLink(string $file, string $label, string $current): string {
    $active = ($file === $current);
    $bg = $active ? '#4CAF50' : 'transparent';
    return "<a href=\"$file\" style=\"background:$bg;\">$label</a>";
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
  <h2><?= $edit ? 'Edit' : 'Form'; ?> Penilaian Project</h2>
  <form id="form" action="proses.php" method="POST">
    <?php if($edit):?>
      <input type="hidden" name="id" value="<?= $id;?>">
    <?php endif;?>
    <label>Nama Project:</label>
    <input type="text" id="pname" name="project_name" required
           value="<?= htmlspecialchars($data['project_name']); ?>">
    <br><br>
    <table id="tbl">
      <thead>
        <tr>
          <th style="width:25%">Parameter</th>
          <th>Sub-Aspek</th>
          <th style="width:15%">Jumlah Kesalahan</th>
          <th style="width:10%"></th>
        </tr>
      </thead>
      <tbody id="tbody"></tbody>
    </table>
    <button type="button" class="btn btn-success" onclick="addAspect()">+ Parameter</button>
    <br><br>
    <input type="hidden" name="detail" id="detail">
    <button type="submit" class="btn btn-warning">
      <?= $edit ? 'Update' : 'Simpan'; ?>
    </button>
  </form>
</div>

<script>
  const tbody = document.getElementById('tbody');
  const form  = document.getElementById('form');

  function addAspect(name = '', subs = []) {
    const firstRow = document.createElement('tr');
    firstRow.className = 'aspect-first';
    firstRow.innerHTML = `
      <td rowspan="1"><input class="a-name" placeholder="Nama Parameter" value="${name}"></td>
      <td><input class="s-name" placeholder="Nama Sub" value="${subs[0]?.name||''}"></td>
      <td><input type="number" min="0" class="s-mist num" value="${subs[0]?.mistake||0}"></td>
      <td rowspan="1">
        <button type="button" class="btn btn-primary" onclick="addSub(this)">+ Sub</button><br>
        <button type="button" class="btn btn-danger"  onclick="removeAspect(this)">Hapus</button>
      </td>`;
    tbody.appendChild(firstRow);
    subs.slice(1).forEach(s => addSub(firstRow.querySelector('.btn-primary'), s.name, s.mistake));
  }

  function removeAspect(btn) {
    const rows = getAspectRows(btn.closest('tr'));
    rows.forEach(r => r.remove());
  }

  function addSub(btn, name = '', mist = 0) {
    const first = btn.closest('tr');
    const rows  = getAspectRows(first);
    const last  = rows[rows.length - 1];
    const row   = document.createElement('tr');
    row.innerHTML = `
      <td><input class="s-name" placeholder="Nama Sub" value="${name}"></td>
      <td><input type="number" min="0" class="s-mist num" value="${mist}"></td>`;
    last.parentNode.insertBefore(row, last.nextSibling);
    first.cells[0].rowSpan++;
    first.cells[3].rowSpan++;
  }

  function getAspectRows(first) {
    const rows = [first];
    let nxt = first.nextElementSibling;
    while (nxt && !nxt.classList.contains('aspect-first')) {
      rows.push(nxt);
      nxt = nxt.nextElementSibling;
    }
    return rows;
  }

  // Inisialisasi data default atau edit
  <?php if($edit): ?>
    const data = <?= json_encode($data['aspects']); ?>;
    data.forEach(a => addAspect(a.name, a.subs));
  <?php else: ?>
    addAspect('Penguasaan Materi', [
      {name:'Materi Basis Data', mistake:0},
      {name:'Materi Struktur',  mistake:0},
      {name:'Materi Matematika',mistake:0},
      {name:'Materi',           mistake:0}
    ]);
    addAspect('Celah Keamanan', [
      {name:'Sanitasi',     mistake:0},
      {name:'Authorization',mistake:0}
    ]);
    addAspect('Fitur Utama', [
      {name:'Create',mistake:0},
      {name:'Read',  mistake:0},
      {name:'Update',mistake:0},
      {name:'Delete',mistake:0}
    ]);
    addAspect('Fitur Pendukung', [
      {name:'Responsive', mistake:0},
      {name:'Load Time',  mistake:0}
    ]);
  <?php endif; ?>

  form.addEventListener('submit', e => {
    const aspects = [];
    document.querySelectorAll('.aspect-first').forEach(first => {
      const aName = first.querySelector('.a-name').value.trim();
      if (!aName) return;
      const subs = [];
      subs.push({
        name:    first.querySelector('.s-name').value.trim(),
        mistake: parseInt(first.querySelector('.s-mist').value||0, 10)
      });
      let next = first.nextElementSibling;
      while (next && !next.classList.contains('aspect-first')) {
        subs.push({
          name:    next.querySelector('.s-name').value.trim(),
          mistake: parseInt(next.querySelector('.s-mist').value||0, 10)
        });
        next = next.nextElementSibling;
      }
      aspects.push({ name: aName, subs });
    });
    document.getElementById('detail').value = JSON.stringify({
      project_name: document.getElementById('pname').value.trim(),
      aspects
    });
  });
</script>
</body>
</html>
