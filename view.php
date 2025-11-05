<?php
// DB config (env or local)
$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$db   = getenv('DB_NAME') ?: 'registration_db';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

    // Handle delete all (GET param)
    if (isset($_GET['deleteall'])) {
        $pdo->exec("TRUNCATE TABLE registrations");
        header("Location: view.php");
        exit;
    }

    $stmt = $pdo->query("SELECT id, fullname, email, phone, created_at FROM registrations ORDER BY id DESC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("DB error: " . htmlspecialchars($e->getMessage()));
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Registrations</title>
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
  width:90%;
  max-width:900px;
  padding:24px;
  border:1px solid #ccc;
  border-radius:10px;
  text-align:center;
}
table{
  width:100%;
  border-collapse:collapse;
  margin-top:16px;
}
th, td{
  padding:10px;
  border:1px solid #ddd;
  text-align:left;
}
th{
  background:#f5f5f5;
}
.controls{margin-top:16px;}
button{padding:10px 16px;margin-right:10px;cursor:pointer;border-radius:6px;border:1px solid #333;background:#fff;}
.delete{background:#ff4d4d;color:white;border-color:#ff4d4d;}
</style>
</head>
<body>
  <div class="card">
    <h2>Registered Candidates</h2>

    <table>
      <tr>
        <th>ID</th>
        <th>Full Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Registered At</th>
      </tr>

      <?php if (empty($rows)): ?>
        <tr><td colspan="5" style="text-align:center;padding:14px;">No records found</td></tr>
      <?php else: ?>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['id']) ?></td>
            <td><?= htmlspecialchars($r['fullname']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td><?= htmlspecialchars($r['phone']) ?></td>
            <td><?= htmlspecialchars($r['created_at']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </table>

    <div class="controls">
      <a href="index.html"><button>Back</button></a>
      <a href="view.php?deleteall=yes" onclick="return confirm('Delete ALL records?');">
        <button class="delete">Delete All</button>
      </a>
    </div>
  </div>
</body>
</html>
