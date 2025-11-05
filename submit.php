<?php
// Get POST values safely
$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');

// DB config via env vars (or local XAMPP defaults)
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_NAME') ?: 'registration_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // ensure table exists (defensive)
    $pdo->exec("
      CREATE TABLE IF NOT EXISTS registrations (
        id INT AUTO_INCREMENT PRIMARY KEY,
        fullname VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone VARCHAR(50),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
    ");

    // insert using prepared statement
    $stmt = $pdo->prepare("INSERT INTO registrations (fullname, email, phone) VALUES (:f, :e, :p)");
    $stmt->execute([':f' => $fullname, ':e' => $email, ':p' => $phone]);

} catch (PDOException $e) {
    // For debugging locally you can echo this; on production log instead
    die("Database error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Submitted</title>
<style>
body{
  font-family:Arial, sans-serif;
  margin:0;
  height:100vh;
  display:flex;
  justify-content:center;
  align-items:center;
  background:white;
}
.card{
  width:380px;
  padding:28px;
  border:1px solid #ccc;
  border-radius:10px;
  text-align:center;
}
h2{margin:0 0 10px 0;}
button{padding:10px 20px;margin-top:18px;cursor:pointer;border-radius:6px;border:1px solid #333;background:#fff;}
</style>
</head>
<body>
  <div class="card">
    <h2>✅ Registered Successfully</h2>
    <p>Your details have been saved.</p>

    <a href="index.html"><button>Back to Registration</button></a>
  </div>
</body>
</html>
