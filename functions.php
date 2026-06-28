<?php
require_once __DIR__ . '/config.php';

function h($value)
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

function site_url($path = '')
{
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
    return $base === '/' ? "/$path" : "$base/$path";
}

function render_header($title = 'Capstone Portal')
{
    $user = current_user();
    $flash = get_flash();
    ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo h($title); ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="assets/css/style-overrides.css">
</head>
<body>
<header class="site-header">
    <div class="container">
        <a class="brand" href="index.php">Capstone Portal</a>
        <nav>
            <a href="index.php">Home</a>
            <?php if ($user): ?>
                <a href="dashboard.php">Dashboard</a>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
            <?php endif; ?>
        </nav>
    </div>
</header>
<main class="container">
    <?php if (!empty($flash)): ?>
        <div class="flash-box">
            <?php foreach ($flash as $type => $message): ?>
                <div class="flash <?php echo h($type); ?>"><?php echo h($message); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <?php
}

function render_footer()
{
    ?>
</main>
<footer class="site-footer">
    <div class="container">
        <p>&copy; <?php echo date('Y'); ?> Capstone Portal. Built for deployment on shared hosting.</p>
    </div>
</footer>
<script src="assets/js/app.js"></script>
</body>
</html>
    <?php
}
