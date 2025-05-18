<!DOCTYPE html>
<html>
<head>
    <title>Error | Slay the Spire Recreation</title>
    <link rel="stylesheet" href="static/css/font.css">
    <link rel="stylesheet" href="static/css/reward.css">
</head>
<body>
    <div class="reward-container">
        <h1>Error</h1>
        <p><?php echo htmlspecialchars(
            $message ?? "An unknown error has occurred."
        ); ?></p>

        <button onclick="window.location.href='map'">Return to Map</button>
    </div>
</body>
</html>
