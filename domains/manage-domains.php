<?php
/**
 * @fileoverview Manage Domains page for Aspect Dashboard.
 *
 * PHP admin panel page to view, add, edit, and delete domain records.
 *
 * Part of the open-source AspectSoft project collection.
 * © 2025 AspectSoft – MIT License
 * https://github.com/dmsfiris/aspect-dashboard
 */

require_once __DIR__ . '/../config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$success = "";
$editDomain = null;

// API base URLs
$apiDomainsBase = 'http://localhost:5000/api/domains';
$apiStoresBase = 'http://localhost:5000/api/stores';

// Helper: GET request
function apiGet($url) {
    $response = @file_get_contents($url);
    return $response ? json_decode($response, true) : [];
}

// Helper: POST, PUT, DELETE requests
function apiRequest($url, $method, $data = []) {
    $opts = [
        'http' => [
            'method'  => $method,
            'header'  => "Content-Type: application/json\r\n",
            'content' => json_encode($data),
            'timeout' => 5
        ]
    ];
    $context = stream_context_create($opts);
    $result = @file_get_contents($url, false, $context);
    return $result ? json_decode($result, true) : false;
}

// Load domains and stores
$domains = apiGet($apiDomainsBase);
$stores = apiGet($apiStoresBase);

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = intval($_GET['delete']);
    $result = apiRequest("$apiDomainsBase/$deleteId", 'DELETE');
    if ($result) {
        $success = "Domain deleted successfully.";
        header("Location: manage-domains.php"); exit;
    } else {
        $errors[] = "Failed to delete domain.";
    }
}

// Handle edit form load
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    foreach ($domains as $d) {
        if ($d['id'] == $editId) {
            $editDomain = $d;
            break;
        }
    }
    if (!$editDomain) $errors[] = "Domain not found for editing.";
}

// Handle form submission (create or update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $domain = trim($_POST['domain'] ?? '');
    $status = trim($_POST['status'] ?? '');
    $store_id = isset($_POST['store_id']) && $_POST['store_id'] !== '' ? intval($_POST['store_id']) : null;
    $id = isset($_POST['id']) ? intval($_POST['id']) : null;

    if ($domain === '' || $status === '') {
        $errors[] = "Domain and status are required.";
    } else {
        $data = [
            'domain' => $domain,
            'status' => $status,
            'store_id' => ($store_id > 0) ? $store_id : null
        ];

        if ($id) {
            $response = apiRequest("$apiDomainsBase/$id", 'PUT', $data);
            $success = $response ? "Domain updated successfully." : "Failed to update domain.";
        } else {
            $response = apiRequest($apiDomainsBase, 'POST', $data);
            $success = $response ? "Domain added successfully." : "Failed to add domain.";
        }

        if (!$response) $errors[] = $success;
        else header("Location: manage-domains.php"); exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Domains</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main id="dashboard-main">
  <div class="dashboard-header">
    <h1>Manage Domains</h1>
    <p>View, add, edit, or delete domain information.</p>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="alert error"><?php foreach ($errors as $error) echo "<p>$error</p>"; ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert success"><p><?= htmlspecialchars($success) ?></p></div>
  <?php endif; ?>

  <!-- Domain Form -->
  <section class="dashboard-section">
    <h2><?= $editDomain ? 'Edit Domain' : 'Add New Domain' ?></h2>
    <form method="POST" class="form-grid">
      <?php if ($editDomain): ?>
        <input type="hidden" name="id" value="<?= htmlspecialchars($editDomain['id']) ?>">
      <?php endif; ?>

      <label for="domain">Domain Name:</label>
      <input type="text" id="domain" name="domain" required
             value="<?= htmlspecialchars($editDomain['domain'] ?? '') ?>">

      <label for="status">Status:</label>
      <input type="text" id="status" name="status" required
             value="<?= htmlspecialchars($editDomain['status'] ?? '') ?>">

      <label for="store-id">Store (optional):</label>
      <select id="store-id" name="store_id">
        <option value="">-- No Store Assigned --</option>
        <?php foreach ($stores as $store): ?>
          <option value="<?= htmlspecialchars($store['id']) ?>"
            <?= isset($editDomain['store_id']) && $editDomain['store_id'] == $store['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($store['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>

      <button type="submit" class="btn"><?= $editDomain ? 'Update' : 'Add' ?> Domain</button>
    </form>
  </section>

  <!-- Domain Table -->
  <section class="dashboard-section">
    <h2>Existing Domains</h2>
    <table class="dashboard-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Domain</th>
          <th>Status</th>
          <th>Store</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($domains)): ?>
          <?php foreach ($domains as $domain): ?>
            <tr>
              <td><?= htmlspecialchars($domain['id']) ?></td>
              <td><?= htmlspecialchars($domain['domain']) ?></td>
              <td><?= htmlspecialchars($domain['status']) ?></td>
              <td>
                <?php
                  $storeName = 'N/A';
                  foreach ($stores as $store) {
                    if ($store['id'] == ($domain['store_id'] ?? null)) {
                      $storeName = $store['name'];
                      break;
                    }
                  }
                  echo htmlspecialchars($storeName);
                ?>
              </td>
              <td>
                <a class="btn small" href="?edit=<?= $domain['id'] ?>">Edit</a>
                <a class="btn small danger" href="?delete=<?= $domain['id'] ?>"
                   onclick="return confirm('Are you sure you want to delete this domain?');">Delete</a>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5">No domains found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>
</main>

<?php include '../includes/footer.php'; ?>
<script src="../assets/js/app.js"></script>
</body>
</html>
