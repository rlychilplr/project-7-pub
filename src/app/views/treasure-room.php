<!DOCTYPE html>
<html>
<head>
    <title>Treasure Room | Slay the Spire Recreation</title>
    <link rel="stylesheet" href="static/css/font.css">
    <link rel="stylesheet" href="static/css/reward.css">
    <link rel="stylesheet" href="static/css/treasure-room.css">
</head>
<body>
    <div class="reward-container treasure-container">
        <h1>Treasure Room</h1>
        <p><?php echo htmlspecialchars($message); ?></p>

        <?php if (!isset($goldOnly)): ?>
            <div class="treasure-chest">
                <img src="static/images/treasure-chest.png" alt="Treasure Chest" id="chest-image">
            </div>

            <div class="relic-container" style="display: none;" id="relic-reveal">
                <?php
                // Define relic details - in production this would come from a proper model
                $relicDetails = [
                    "akabeko" => [
                        "name" => "Akabeko",
                        "description" =>
                            "Your first attack each combat deals 8 additional damage.",
                        "image" =>
                            "static/images/temp/images/largeRelics/akabeko.png",
                    ],
                    "oddly_smooth_stone" => [
                        "name" => "Oddly Smooth Stone",
                        "description" =>
                            "At the start of each combat, gain 1 Dexterity.",
                        "image" =>
                            "static/images/temp/images/largeRelics/smooth_stone.png",
                    ],
                    "pen_nib" => [
                        "name" => "Pen Nib",
                        "description" =>
                            "Every 10th Attack deals double damage.",
                        "image" =>
                            "static/images/temp/images/largeRelics/penNib.png",
                    ],
                    "vajra" => [
                        "name" => "Vajra",
                        "description" =>
                            "At the start of each combat, gain 1 Strength.",
                        "image" =>
                            "static/images/temp/images/largeRelics/vajra.png",
                    ],
                ];

                $relic = $relicDetails[$relicId] ?? null;

                if ($relic): ?>
                    <img src="<?php echo $relic[
                        "image"
                    ]; ?>" alt="<?php echo $relic[
    "name"
]; ?>" class="relic-image">
                    <div class="relic-name"><?php echo $relic["name"]; ?></div>
                    <div class="relic-description"><?php echo $relic[
                        "description"
                    ]; ?></div>
                <?php endif;
                ?>
            </div>
        <?php else: ?>
            <div class="gold-container">
                <img src="static/images/gold.png" alt="Gold" class="gold-image">
                <div class="gold-amount">+25 Gold</div>
            </div>
        <?php endif; ?>

        <form method="POST" action="treasure">
            <button id="continue-btn"><?php echo isset($goldOnly)
                ? "Take Gold"
                : "Take Relic"; ?></button>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (!isset($goldOnly)): ?>
            const chestImage = document.getElementById('chest-image');
            const relicReveal = document.getElementById('relic-reveal');
            const continueBtn = document.getElementById('continue-btn');

            // Hide the button initially
            continueBtn.style.display = 'none';

            // Click chest to open it and reveal relic
            chestImage.addEventListener('click', function() {
                chestImage.classList.add('opened');

                // After a short delay, hide chest and show relic
                setTimeout(() => {
                    document.querySelector('.treasure-chest').style.display = 'none';
                    relicReveal.style.display = 'block';
                    continueBtn.style.display = 'block';
                }, 1000);
            });
            <?php endif; ?>
        });
    </script>

    <div class="background-2"></div>
    <div class="background"></div>
</body>
</html>
