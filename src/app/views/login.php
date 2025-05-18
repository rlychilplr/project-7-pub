<?php
/**
 * @var string|null $error
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login | Slay the Spire Recreation</title>
    <link rel="stylesheet" href="static/css/all.min.css">
    <link rel="stylesheet" href="static/css/font.css">
    <link rel="stylesheet" href="static/css/auth.css">
</head>
<body>
<span onclick="window.location.href = './'" class="home"><i class="fa-solid fa-house"></i> Home</span>
    <div class="auth-container">
        <h1>Login</h1>

        <?php if (isset($error)): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="login">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Login</button>
        </form>

        <p class="register-link">
            Don't have an account? <a href="register">Register here</a>
        </p>
    </div>
</body>
</html>
