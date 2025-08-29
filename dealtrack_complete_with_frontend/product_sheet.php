<?php include 'includes/navbar.php'; include 'config.php'; ?>
<!doctype html><html><head><meta charset='utf-8'><title>Products</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class='container py-4'>
<h1>Product Catalog</h1>
<table class='table'><thead><tr><th>Product</th><th>Category</th><th>Latest price</th><th>Compare</th></tr></thead><tbody>
<?php $res = $conn->query("SELECT p.id, p.product_name, p.category, (SELECT price FROM prices pr WHERE pr.product_id=p.id ORDER BY pr.date_checked DESC LIMIT 1) AS latest_price FROM products p ORDER BY p.product_name ASC"); while ($r = $res->fetch_assoc()) { echo '<tr><td>'.htmlspecialchars($r['product_name']).'</td><td>'.htmlspecialchars($r['category']).'</td><td>'.($r['latest_price']? 'R'.number_format($r['latest_price'],2):'—').'</td><td><a class="btn btn-sm btn-outline-primary" href="compare.php?q='.urlencode($r['product_name']).'">Compare</a></td></tr>'; } ?>
</tbody></table>
<?php include 'includes/footer.php'; ?></body></html>