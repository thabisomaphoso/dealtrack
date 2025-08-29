<?php include 'includes/navbar.php'; include 'config.php'; if (!is_admin()) { header('Location: login.php'); exit; } $msg=''; $id = intval($_GET['id'] ?? 0); $row = null; if ($id) { $r = $conn->query('SELECT * FROM products WHERE id='.$id); $row = $r->fetch_assoc(); }
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $id) {
  $name = $_POST['product_name'] ?? '';
  $category = $_POST['category'] ?? '';
  $brand = $_POST['brand'] ?? '';
  $desc = $_POST['description'] ?? '';
  if (!empty($_FILES['image']['name'])) {
    if (!is_dir('uploads/images')) mkdir('uploads/images', 0777, true);
    $fn = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/','_', $_FILES['image']['name']);
    $target = 'uploads/images/' . $fn;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) { $imgPath = $target; $conn->query("UPDATE products SET image='".$conn->real_escape_string($imgPath)."' WHERE id=".$id); }
  }
  $stmt = $conn->prepare('UPDATE products SET product_name=?, description=?, category=?, brand=? WHERE id=?');
  $stmt->bind_param('ssssi', $name, $desc, $category, $brand, $id);
  if ($stmt->execute()) { $msg = 'Updated.'; }
}
?>
<!doctype html><html><head><meta charset='utf-8'><title>Edit Product</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class='container py-4'>
<h1>Edit Product</h1><?php if($msg): ?><div class='alert alert-info'><?php echo $msg; ?></div><?php endif; ?>
<form method='post' enctype='multipart/form-data'>
 <div class='mb-3'><label>Product name</label><input class='form-control' name='product_name' value='<?php echo htmlspecialchars($row['product_name'] ?? ''); ?>' required></div>
 <div class='mb-3'><label>Category</label><input class='form-control' name='category' value='<?php echo htmlspecialchars($row['category'] ?? ''); ?>'></div>
 <div class='mb-3'><label>Brand</label><input class='form-control' name='brand' value='<?php echo htmlspecialchars($row['brand'] ?? ''); ?>'></div>
 <div class='mb-3'><label>Description</label><textarea class='form-control' name='description'><?php echo htmlspecialchars($row['description'] ?? ''); ?></textarea></div>
 <div class='mb-3'><label>Image</label><input type='file' class='form-control' name='image'></div>
 <button class='btn btn-primary'>Save</button>
</form>
<?php include 'includes/footer.php'; ?></body></html>