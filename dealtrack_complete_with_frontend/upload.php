<?php include 'includes/navbar.php'; include 'config.php';
$hasSpreadsheet = file_exists(__DIR__ . '/vendor/autoload.php');
$message=''; if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
  $f = $_FILES['file'];
  if ($f['error'] !== UPLOAD_ERR_OK) $message='Upload error';
  else {
    $ext = strtolower(pathinfo($f['name'], PATHINFO_EXTENSION));
    $targetDir = __DIR__ . '/uploads/';
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);
    $target = $targetDir . time().'_'.preg_replace('/[^a-zA-Z0-9._-]/','_',$f['name']);
    move_uploaded_file($f['tmp_name'], $target);
    $stmt = $conn->prepare('INSERT INTO uploads (filename, uploaded_by) VALUES (?, 1)');
    $bn = basename($f['name']); $stmt->bind_param('s', $bn); $stmt->execute(); $stmt->close();
    $rows=[];
    if ($ext==='csv') {
      if (($h=fopen($target,'r'))!==false) { while (($line=fgetcsv($h))!==false) { $rows[]=$line; } fclose($h); }
    } elseif (in_array($ext,['xls','xlsx'])) {
      if (!$hasSpreadsheet) { $message='PhpSpreadsheet not found. Install via Composer.'; }
      else { try { $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($target); $reader->setReadDataOnly(true); $ss = $reader->load($target); $rows = $ss->getActiveSheet()->toArray(); } catch (Exception $e) { $message='Error reading file: '.$e->getMessage(); } }
    } else { $message='Invalid file type'; }
    if (!$message && !empty($rows)) {
      foreach ($rows as $r) {
        if (!is_array($r) || count($r)<2) continue;
        $pname = trim($r[0]); $price = floatval(str_replace(',','', $r[1])); $supplierName = $r[2] ?? '';
        if ($pname==='') continue;
        $stmt = $conn->prepare('SELECT id FROM products WHERE product_name = ? LIMIT 1'); $stmt->bind_param('s',$pname); $stmt->execute(); $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) { $pid = $row['id']; } else { $ins = $conn->prepare('INSERT INTO products (product_name) VALUES (?)'); $ins->bind_param('s',$pname); $ins->execute(); $pid = $ins->insert_id; $ins->close(); }
        $sid = null; if (trim($supplierName) !== '') { $stmt = $conn->prepare('SELECT id FROM suppliers WHERE supplier_name = ? LIMIT 1'); $stmt->bind_param('s',$supplierName); $stmt->execute(); $res = $stmt->get_result(); if ($row = $res->fetch_assoc()) { $sid = $row['id']; } else { $ins = $conn->prepare('INSERT INTO suppliers (supplier_name) VALUES (?)'); $ins->bind_param('s',$supplierName); $ins->execute(); $sid = $ins->insert_id; $ins->close(); } }
        if ($sid) { $date = date('Y-m-d'); $ins = $conn->prepare('INSERT INTO prices (product_id, supplier_id, price, date_checked) VALUES (?,?,?,?)'); $ins->bind_param('iids',$pid,$sid,$price,$date); $ins->execute(); }
      }
      $message = 'Import complete.';
    }
  }
}
?>
<!doctype html><html><head><meta charset='utf-8'><title>Upload</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"></head><body class='container py-4'><?php if($message): ?><div class='alert alert-info'><?php echo htmlspecialchars($message); ?></div><?php endif; ?>
<h1>Upload price list (CSV or Excel)</h1>
<form method='post' enctype='multipart/form-data'><input type='file' name='file' accept='.csv,.xls,.xlsx' class='form-control mb-2' required><button class='btn btn-primary'>Upload & Import</button></form>
<p>Expected columns: <strong>product name</strong>, <strong>price</strong>, <strong>supplier (optional)</strong></p>
<?php include 'includes/footer.php'; ?></body></html>