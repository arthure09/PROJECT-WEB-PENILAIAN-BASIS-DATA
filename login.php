<?php
session_start();
if (isset($_SESSION['user'])) {
    header('Location: index.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    
    if ($username === 'kelompoklima' && $password === 'projectbasdat123') {
        $_SESSION['user'] = $username;
        header('Location: index.php');
        exit;
    } else {
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login | Penilaian Project</title>
  <style>
    
    body {
      margin: 0;
      padding: 0;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: url('assets/background.jpg') no-repeat center center fixed;
      background-size: cover;
      font-family: Arial, sans-serif;
    }
    
    .card {
      background: rgba(255,255,255,0.9);
      border-radius: 24px;
      padding: 40px;
      width: 350px;
      box-shadow: 0 8px 16px rgba(0,0,0,0.1);
      text-align: center;
    }
    
    .card h2 {
      margin: 0 0 24px;
      font-size: 20px;
      color: #333;
    }
    
    .form-group {
      text-align: left;
      margin-bottom: 20px;
    }
    .form-group label {
      display: block;
      margin-bottom: 6px;
      font-weight: bold;
      color: #555;
    }
    .form-group input {
      width: 100%;
      padding: 10px 12px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 14px;
      box-sizing: border-box;
    }
    .form-group input::placeholder {
      color: #aaa;
    }
    
    .btn {
      background: #000;
      color: #fff;
      border: none;
      border-radius: 20px;
      padding: 12px 0;
      width: 100%;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.2s;
    }
    .btn:hover {
      background: #333;
    }
    .error {
      color: #e53935;
      margin-bottom: 16px;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <div class="card">
    <h2>Log In To Your Account</h2>
    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="post" novalidate>
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="enter your username" required autofocus>
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="enter your password" required>
      </div>
      <button type="submit" class="btn">Log In</button>
    </form>
  </div>
</body>
</html>
