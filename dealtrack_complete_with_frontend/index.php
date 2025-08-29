<?php include 'includes/navbar.php'; ?>
<!doctype html><html lang='en'><head><meta charset='utf-8'><title>DealTrack SA</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class='d-flex flex-column min-vh-100'><div class='container my-5'>
<h1>DealTrack SA</h1>
<p>Compare prices from multiple suppliers.</p>
<form class='d-flex mb-3' action='compare.php' method='get'><input class='form-control me-2' name='q' placeholder='Search product...' /><button class='btn btn-primary'>Search</button></form>
<a class='btn btn-success' href='upload.php'>Upload shopping list</a>
<hr />
<h4>Recently checked prices</h4>
<div class='row'>
<?php include 'config.php'; $res = $conn->query("SELECT p.product_name, pr.price FROM products p JOIN prices pr ON pr.product_id = p.id ORDER BY pr.date_checked DESC LIMIT 8"); if ($res) { while ($r = $res->fetch_assoc()) { echo '<div class="col-md-3"><div class="card mb-3"><div class="card-body"><h6>'.htmlspecialchars($r['product_name']).'</h6><p>R'.number_format($r['price'],2).'</p></div></div></div>'; } } ?>
</div>
</div><?php include 'includes/footer.php'; ?><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script></body></html>