<?php include 'includes/navbar.php'; include 'config.php'; if (!is_admin()) { header('Location: login.php'); exit; } ?>
<!doctype html><html><head><meta charset='utf-8'><title>Products</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class='container py-4'>
<h1>Manage Products</h1>
<table class='table'><thead><tr><th>Product</th><th>Category</th><th>Actions</th></tr></thead><tbody>
<?php $res = $conn->query('SELECT * FROM products ORDER BY product_name ASC'); while ($r = $res->fetch_assoc()) { echo '<tr><td>'.htmlspecialchars($r['product_name']).'</td><td>'.htmlspecialchars($r['category']).'</td><td><a class="btn btn-sm btn-primary" href="admin_edit_product.php?id='.$r['id'].'">Edit</a></td></tr>'; } ?>
</tbody></table>
<?php include 'includes/footer.php'; ?></body></html>