<!DOCTYPE html>
<html>
<head>
    <title>Victory! | Slay the Spire Recreation</title>
    <link rel="stylesheet" href="static/css/font.css">
    <link rel="stylesheet" href="static/css/victory.css">
</head>
<body>
    <div class="victory-container">
        <h1>VICTORY!</h1>

        <div class="victory-message">
            <p>You have defeated The Guardian and conquered Act 1!</p>
            <p>Final Score: <?php echo isset($_SESSION["score"])
                ? $_SESSION["score"]
                : 100; ?></p>
        </div>

        <div class="run-stats">
            <h2>Run Statistics</h2>
            <div class="stat-item">
                <span class="stat-label">Floor Reached:</span>
                <span class="stat-value"><?php echo $_SESSION["floor"] ??
                    30; ?></span>
            </div>
            <div class="stat-item">
                <span class="stat-label">Time Elapsed:</span>
                <span class="stat-value"><?php echo isset(
                    $_SESSION["start_time"]
                )
                    ? ceil((time() - $_SESSION["start_time"]) / 60) . " minutes"
                    : "20 minutes"; ?></span>
            </div>
        </div>

        <div class="character-info">
            <img src="static/images/ironclad.png" alt="Character" class="character-image">
            <div class="character-stats">
                <h3><?php echo $_SESSION["characterName"] ?? "Ironclad"; ?></h3>
                <p>HP: <?php echo $_SESSION["playerHealth"] .
                    "/" .
                    $_SESSION["playerMaxHealth"]; ?></p>
                <p>Gold: <?php echo $_SESSION["money"] ?? 0; ?></p>
            </div>
        </div>

        <div class="buttons">
            <button onclick="window.location.href='restart'">Play Again</button>
            <button onclick="window.location.href='./'">Return to Menu</button>
        </div>
    </div>
    <div class="background-2"></div>
    <div class="background"></div>

    <script>
        // Celebratory animation effect
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.querySelector('.victory-container');
            container.classList.add('animate-in');

            // Create confetti effect
            createConfetti();

            function createConfetti() {
                const confettiCount = 100;
                const container = document.body;

                for (let i = 0; i < confettiCount; i++) {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';

                    // Randomize confetti properties
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.animationDelay = Math.random() * 5 + 's';
                    confetti.style.backgroundColor = getRandomColor();

                    container.appendChild(confetti);
                }
            }

            function getRandomColor() {
                const colors = ['#f44336', '#e91e63', '#9c27b0', '#673ab7', '#3f51b5',
                              '#2196f3', '#03a9f4', '#00bcd4', '#009688', '#4caf50',
                              '#8bc34a', '#cddc39', '#ffeb3b', '#ffc107', '#ff9800', '#ff5722'];
                return colors[Math.floor(Math.random() * colors.length)];
            }
        });
    </script>
</body>
</html>
