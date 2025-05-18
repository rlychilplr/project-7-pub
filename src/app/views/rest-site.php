<!DOCTYPE html>
<html>
<head>
    <title>Rest Site | Slay the Spire Recreation</title>
    <link rel="stylesheet" href="static/css/font.css">
    <link rel="stylesheet" href="static/css/rest-site.css">
</head>
<body>
    <div class="page-container">
        <?php require_once "header-game.php"; ?>

        <div class="rest-container">
            <h1>Rest Site</h1>

            <div class="campfire">
                <img src="static/images/campfire.png" alt="Campfire" class="campfire-image">
                <?php if ($isFullHealth): ?>
                    <p class="rest-message">You're already at full health.</p>
                <?php else: ?>
                    <p class="rest-message">Rest here to heal <?php echo $healAmount; ?> HP.</p>
                <?php endif; ?>
            </div>

            <div class="health-info">
                <div class="health-bar-container">
                    <div class="health-bar" style="width: <?php echo ($currentHP /
                        $maxHP) *
                        100; ?>%"></div>
                </div>
                <div class="health-text">
                    <?php echo $currentHP; ?>/<?php echo $maxHP; ?> HP
                </div>

                <?php if (!$isFullHealth): ?>
                    <div class="health-preview">
                        <div class="health-bar-container">
                            <div class="health-bar" style="width: <?php echo (min(
                                $currentHP + $healAmount,
                                $maxHP
                            ) /
                                $maxHP) *
                                100; ?>%"></div>
                        </div>
                        <div class="health-text">
                            <?php echo min(
                                $currentHP + $healAmount,
                                $maxHP
                            ); ?>/<?php echo $maxHP; ?> HP after rest
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <form method="POST" action="rest">
                <button type="submit" id="rest-btn">Rest</button>
            </form>
        </div>
    </div>

    <div class="background-2"></div>
    <div class="background"></div>
</body>
</html>
