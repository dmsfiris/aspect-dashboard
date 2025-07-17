<?php
/**
 * @fileoverview Manage Stores page for Aspect Dashboard.
 *
 * PHP admin panel page to view, add, and manage store information.
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

// API base URL
$apiBase = 'http://localhost:5000/api/stores';

// Helper: Perform GET request to API
function apiGetStores() {
    global $apiBase;
    $response = @file_get_contents($apiBase);
    if ($response === false) {
        return [];
    }
    return json_decode($response, true) ?: [];
}

// Helper: Perform POST request to API
function apiAddStore($data) {
    global $apiBase;
    $payload = json_encode($data);

    $opts = [
        'http' => [
            'method'  => 'POST',
            'header'  => "Content-Type: application/json\r\n",
            'content' => $payload,
            'timeout' => 5
        ]
    ];

    $context = stream_context_create($opts);
    $result = @file_get_contents($apiBase, false, $context);
    if ($result === false) {
        return false;
    }
    return json_decode($result, true);
}

// Handle new store submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $owner = trim($_POST['owner'] ?? '');
    $email = trim($_POST['contact_email'] ?? '');

    // Basic validation
    if ($name === '' || $owner === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "All fields are required and email must be valid.";
    } else {
        // Prepare data to send
        $newStore = [
            'name' => $name,
            'owner' => $owner,
            'contact_email' => $email
        ];

        // Call API to add store
        $response = apiAddStore($newStore);
        if ($response && isset($response['id'])) {
            $success = "Store added successfully.";
        } else {
            $errors[] = "Failed to add store via API.";
        }
    }
}

// Load stores from API
$stores = apiGetStores();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Stores</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<main id="dashboard-main">
  <div class="dashboard-header">
    <h1>Manage Stores</h1>
    <p>View, add, or manage store information.</p>
  </div>

  <?php if (!empty($errors)): ?>
    <div class="alert error">
        <?php foreach ($errors as $error) echo "<p>$error</p>"; ?>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert success">
        <p><?= htmlspecialchars($success) ?></p>
    </div>
  <?php endif; ?>

  <!-- Add Store Form -->
  <section class="dashboard-section">
    <h2>Add New Store</h2>
    <form method="POST" class="form-grid">
      <label for="store-name">Store Name:</label>
      <input type="text" id="store-name" name="name" required>

      <label for="owner">Owner:</label>
      <input type="text" id="owner" name="owner" required>

      <label for="contact-email">Contact Email:</label>
      <input type="email" id="contact-email" name="contact_email" required>

      <button type="submit" class="btn">Add Store</button>
    </form>
  </section>

  <!-- Existing Stores Table -->
  <section class="dashboard-section">
    <h2>Existing Stores</h2>
    <table class="dashboard-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Owner</th>
          <th>Email</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($stores)): ?>
          <?php foreach ($stores as $store): ?>
            <tr>
              <td><?= htmlspecialchars($store['id']) ?></td>
              <td><?= htmlspecialchars($store['name']) ?></td>
              <td><?= htmlspecialchars($store['owner']) ?></td>
              <td><?= htmlspecialchars($store['contact_email']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="4">No stores found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </section>
</main>

<?php include '../includes/footer.php'; ?>

<!-- Main JS -->
<script src="../assets/js/app.js"></script>
</body>
</html>
