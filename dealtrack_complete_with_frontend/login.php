<?php include 'includes/navbar.php'; include 'config.php';
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';
  $stmt = $conn->prepare('SELECT id, username, password_hash, role FROM users WHERE username = ? LIMIT 1');
  $stmt->bind_param('s', $username);
  $stmt->execute();
  $res = $stmt->get_result();
  if ($row = $res->fetch_assoc()) {
    if (password_verify($password, $row['password_hash'])) {
      $_SESSION['user'] = ['id'=>$row['id'],'username'=>$row['username'],'role'=>$row['role']];
      header('Location: dashboard.php'); exit;
    } else { $msg = 'Invalid credentials'; }
  } else { $msg = 'Invalid credentials'; }
}
?>
<!doctype html><html><head><meta charset='utf-8'><title>Login</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class='container py-4'><?php if($msg): ?><div class='alert alert-danger'><?php echo $msg; ?></div><?php endif; ?>
<h1>Login</h1>
<form method='post'><div class='mb-3'><label>Username</label><input class='form-control' name='username' required></div><div class='mb-3'><label>Password</label><input type='password' class='form-control' name='password' required></div><button class='btn btn-primary'>Login</button></form></body></html>