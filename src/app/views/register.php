<?php
/**
 * @var array $errors
 * @var string|null $username
 * @var string|null $email
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register | Slay the Spire Recreation</title>
    <link rel="stylesheet" href="static/css/all.min.css">
    <link rel="stylesheet" href="static/css/font.css">
    <link rel="stylesheet" href="static/css/auth.css">
</head>
<body>
    <span onclick="window.location.href = './'" class="home"><i class="fa-solid fa-house"></i> Home</span>
    <div class="auth-container">
        <h1>Register</h1>

        <?php if (isset($errors["general"])): ?>
            <div class="error-message"><?= htmlspecialchars(
                $errors["general"]
            ) ?></div>
        <?php endif; ?>

        <form method="POST" action="register">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username"
                       value="<?= htmlspecialchars(
                           $username ?? ""
                       ) ?>" required autofocus>
                <?php if (isset($errors["username"])): ?>
                    <span class="error"><?= htmlspecialchars(
                        $errors["username"]
                    ) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email"
                       value="<?= htmlspecialchars($email ?? "") ?>" required>
                <?php if (isset($errors["email"])): ?>
                    <span class="error"><?= htmlspecialchars(
                        $errors["email"]
                    ) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
                <?php if (isset($errors["password"])): ?>
                    <span class="error"><?= htmlspecialchars(
                        $errors["password"]
                    ) ?></span>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
                <?php if (isset($errors["confirm_password"])): ?>
                    <span class="error"><?= htmlspecialchars(
                        $errors["confirm_password"]
                    ) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit">Register</button>
        </form>

        <p class="login-link">
            Already have an account? <a href="login">Login here</a>
        </p>
    </div>
</body>
</html>
