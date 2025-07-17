<?php
/**
 * @fileoverview Landing page with a login form to access the Aspect Dashboard.
 *
 * Provides user authentication to access dashboard features.
 *
 * Part of the open-source AspectSoft project collection.
 * © 2025 AspectSoft – MIT License
 * https://github.com/dmsfiris/aspect-dashboard
 */


// Start session to manage login state
session_start();

// Dummy login credentials for demonstration (replace with real auth)
$valid_username = 'admin';
$valid_password = 'password';

// Initialize error message
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    // Simple authentication check
    if ($username === $valid_username && $password === $valid_password) {
        $_SESSION['logged_in'] = true;
        // Redirect to dashboard after successful login
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Invalid username or password.';
    }
}

// If already logged in, redirect to dashboard
if (!empty($_SESSION['logged_in'])) {
    header('Location: dashboard.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Aspect Dashboard Login - AspectSoft</title>
  <link rel="stylesheet" href="assets/css/login.css" />
</head>
<body>
  <main class="login-container" role="main" aria-label="Login Form">
    <h1>Aspect Dashboard</h1>

    <?php if ($error): ?>
      <div class="error" role="alert"><?=htmlspecialchars($error)?></div>
    <?php endif; ?>

    <form method="post" action="index.php" novalidate>
      <label for="username">Username</label>
      <input
        type="text"
        id="username"
        name="username"
        required
        autocomplete="username"
        autofocus
        placeholder="Enter your username"
      />

      <label for="password">Password</label>
      <input
        type="password"
        id="password"
        name="password"
        required
        autocomplete="current-password"
        placeholder="Enter your password"
      />

      <button type="submit">Login</button>
    </form>
  </main>
</body>
</html>
