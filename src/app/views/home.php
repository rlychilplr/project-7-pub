<?php
/**
 * @var bool $isLoggedIn
 * @var array|null $user
 */
?>

<!DOCTYPE html>
<html>
<head>
    <title>Slay the Spire Recreation</title>
    <link rel="stylesheet" href="static/css/all.min.css">
    <link rel="stylesheet" href="static/css/font.css">
    <link rel="stylesheet" href="static/css/home.css">
</head>
<body>
    <?php if ($isLoggedIn && $user): ?>
        <button id="logout-button" onclick="window.location.href = 'logout'">
            <i class="fa-solid fa-right-from-bracket"></i> Logout (<?= htmlspecialchars(
                $user["username"] ?? ""
            ) ?>)
        </button>

    <?php else: ?>
        <button id="login-button" onclick="window.location.href = 'login'">
            <i class="fa-solid fa-right-to-bracket"></i> Login
        </button>

    <?php endif; ?>

    <h2 class="play-button" onclick="window.location.href = 'map'">
        <i class="fa-solid fa-play"></i> Play
    </h2>

    <nav>
        <h2 onclick="window.location.href = 'compendium'">Compendium</h2>
        <h2 onclick="window.location.href = 'statistics'">Statistics</h2>
        <h2 onclick="window.location.href = 'settings'">Settings</h2>
    </nav>
    <script src="static/js/main.js"></script>
</body>
</html>
