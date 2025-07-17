<?php
/**
 * @fileoverview Main Dashboard page for Aspect Dashboard.
 *
 * PHP admin panel homepage displaying overview and quick access to features.
 *
 * Part of the open-source AspectSoft project collection.
 * © 2025 AspectSoft – MIT License
 * https://github.com/dmsfiris/aspect-dashboard
 */

require_once __DIR__ . '/config.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$page_title = 'Aspect Dashboard';

// Node.js API base URL
$apiBaseUrl = 'http://localhost:5000/api';

// Fetch data from API helper function
function fetchFromApi($url) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        curl_close($ch);
        return null;
    }
    curl_close($ch);
    return json_decode($response, true);
}

// Fetch stores from Node.js API
$stores = fetchFromApi("$apiBaseUrl/stores") ?: [];

// Load domains from JSON file locally
$domainsFile = __DIR__ . '/domains/domains.json';
$domainsData = file_exists($domainsFile) ? file_get_contents($domainsFile) : '[]';
$domains = json_decode($domainsData, true) ?: [];

// Calculate dashboard stats
$totalStores = count($stores);
$activeDomains = 0;
$pendingApprovals = 0;
$supportTickets = 5; // Placeholder for demo

foreach ($domains as $domain) {
    if (strtolower($domain['status']) === 'active') {
        $activeDomains++;
    } elseif (stripos($domain['status'], 'pending') !== false) {
        $pendingApprovals++;
    }
}

// Helper function to get store name by store_id
function getStoreNameById($stores, $store_id) {
    foreach ($stores as $store) {
        if ($store['id'] === $store_id) {
            return $store['name'];
        }
    }
    return 'Unknown Store';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= htmlspecialchars($page_title) ?></title>

  <!-- Favicon -->
  <link rel="icon" href="/favicon.ico" type="image/x-icon" />

  <!-- Main CSS -->
  <link rel="stylesheet" href="assets/css/dashboard.css" />
</head>
<body>

<?php include 'includes/header.php'; ?>
<?php include 'includes/sidebar.php'; ?>

<main id="dashboard-main">

    <!-- Page Header -->
    <div class="dashboard-header">
        <h1>Aspect Admin Dashboard</h1>
        <p>Manage stores, domains, and view system summaries.</p>
    </div>

    <!-- Summary Cards -->
    <div class="dashboard-cards">
        <div class="card"><h2>Total Stores</h2><p><?= $totalStores ?></p></div>
        <div class="card"><h2>Active Domains</h2><p><?= $activeDomains ?></p></div>
        <div class="card"><h2>Pending Approvals</h2><p><?= $pendingApprovals ?></p></div>
        <div class="card"><h2>Support Tickets</h2><p><?= $supportTickets ?></p></div>
    </div>

    <!-- My Stores List -->
    <section class="dashboard-section">
        <h2>My Stores</h2>
        <ul class="store-list">
            <?php foreach ($stores as $store): ?>
                <li>
                    <strong>Store Name:</strong> <?= htmlspecialchars($store['name']) ?><br>
                    <strong>Owner:</strong> <?= htmlspecialchars($store['owner']) ?><br>
                    <strong>Contact:</strong> <?= htmlspecialchars($store['contact_email']) ?><br>
                    <button class="btn-small">Manage</button>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <!-- Domain Management Section -->
    <section class="dashboard-section">
        <h2>Domain Overview</h2>
        <table class="dashboard-table">
            <thead>
                <tr>
                    <th>Domain</th>
                    <th>Status</th>
                    <th>Store Linked</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($domains as $domain): ?>
                    <tr>
                        <td><?= htmlspecialchars($domain['domain']) ?></td>
                        <td><?= htmlspecialchars($domain['status']) ?></td>
                        <td><?= htmlspecialchars(getStoreNameById($stores, $domain['store_id'])) ?></td>
                        <td><button class="btn-small">Edit</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </section>

</main>

<?php include 'includes/footer.php'; ?>

<!-- Main JS -->
<script src="assets/js/app.js"></script>
</body>
</html>
