<!DOCTYPE html>
<html>
<head>
    <title>Game Over | Slay the Spire Recreation</title>
    <link rel="stylesheet" href="../static/css/font.css">
    <link rel="stylesheet" href="../static/css/game-over.css">
</head>
<body>
    <div class="game-over-container">
        <h1>Game Over</h1>
        <p>You have been defeated!</p>

        <div class="stats">
            <h2>Run Statistics</h2>
            <div class="stat-item">
                <span class="stat-label">Floor Reached:</span>
                <span class="stat-value"><?php echo $_SESSION["floor"] ??
                    0; ?></span>
            </div>
        </div>

        <div class="buttons">
            <button onclick="window.location.href='../restart'">Try Again</button>
            <button onclick="window.location.href='../'">Return to Menu</button>
        </div>
    </div>
    <div class="background-2"></div>
    <div class="background"></div>
</body>
</html>
