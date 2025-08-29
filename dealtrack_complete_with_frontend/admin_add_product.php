<?php include 'includes/navbar.php'; include 'config.php'; if (!is_admin()) { header('Location: login.php'); exit; }
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['product_name'] ?? '';
  $category = $_POST['category'] ?? '';
  $brand = $_POST['brand'] ?? '';
  $desc = $_POST['description'] ?? '';
  $imgPath = null;
  if (!empty($_FILES['image']['name'])) {
    if (!is_dir('uploads/images')) mkdir('uploads/images', 0777, true);
    $fn = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/','_', $_FILES['image']['name']);
    $target = 'uploads/images/' . $fn;
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) { $imgPath = $target; }
  }
  $stmt = $conn->prepare('INSERT INTO products (product_name, description, category, brand, image) VALUES (?,?,?,?,?)');
  $stmt->bind_param('sssss', $name, $desc, $category, $brand, $imgPath);
  if ($stmt->execute()) { $msg = 'Product added.'; } else { $msg = 'DB error.'; }
}
?>
<!doctype html><html><head><meta charset='utf-8'><title>Add Product</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class='container py-4'>
<h1>Add Product</h1>
<?php if($msg): ?><div class='alert alert-info'><?php echo $msg; ?></div><?php endif; ?>
<form method='post' enctype='multipart/form-data'>
 <div class='mb-3'><label>Product name</label><input class='form-control' name='product_name' required></div>
 <div class='mb-3'><label>Category</label><input class='form-control' name='category'></div>
 <div class='mb-3'><label>Brand</label><input class='form-control' name='brand'></div>
 <div class='mb-3'><label>Description</label><textarea class='form-control' name='description'></textarea></div>
 <div class='mb-3'><label>Image</label><input type='file' class='form-control' name='image'></div>
 <button class='btn btn-primary'>Add Product</button>
</form>
<?php include 'includes/footer.php'; ?></body></html>