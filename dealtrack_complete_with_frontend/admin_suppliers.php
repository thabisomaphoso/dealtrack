<?php include 'includes/navbar.php'; include 'config.php'; if (!is_admin()) { header('Location: login.php'); exit; } $msg=''; if ($_SERVER['REQUEST_METHOD'] === 'POST') { $name = $_POST['supplier_name'] ?? ''; $website = $_POST['website'] ?? ''; $stmt = $conn->prepare('INSERT INTO suppliers (supplier_name, website) VALUES (?,?)'); $stmt->bind_param('ss', $name, $website); $stmt->execute(); $msg='Added'; }
?>
<!doctype html><html><head><meta charset='utf-8'><title>Suppliers</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class='container py-4'>
<h1>Suppliers</h1>
<?php if($msg): ?><div class='alert alert-info'><?php echo $msg; ?></div><?php endif; ?>
<form method='post'><div class='mb-3'><label>Name</label><input class='form-control' name='supplier_name' required></div><div class='mb-3'><label>Website</label><input class='form-control' name='website'></div><button class='btn btn-primary'>Add Supplier</button></form>
<hr />
<table class='table'><thead><tr><th>Name</th><th>Website</th></tr></thead><tbody><?php $res = $conn->query('SELECT * FROM suppliers ORDER BY supplier_name ASC'); while ($r = $res->fetch_assoc()) { echo '<tr><td>'.htmlspecialchars($r['supplier_name']).'</td><td>'.htmlspecialchars($r['website']).'</td></tr>'; } ?></tbody></table>
<?php include 'includes/footer.php'; ?></body></html>