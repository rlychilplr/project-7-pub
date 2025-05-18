<!DOCTYPE html>
<html>
<head>
    <title>Slay the Spire Recreation</title>
    <link rel="stylesheet" href="static/css/font.css">
    <link rel="stylesheet" href="static/css/battle.css">
    <link rel="stylesheet" href="static/css/all.min.css">
    <link rel="stylesheet" href="static/css/status.css">
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.card');

        cards.forEach(card => {
            card.addEventListener('click', async function() {
                const cardId = this.dataset.cardId;
                console.log('Playing card with ID:', cardId);

                try {
                    const response = await fetch('game/playCard', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ cardId: cardId })
                    });

                    // Check if the response is ok
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();
                    console.log('Server response:', result);

                    if (result.success) {
                        updateBattleState(
                            result.playerHealth,
                            result.enemyHealth,
                            result.playerEnergy,
                            result.playerBlock,
                            result.enemyBlock,
                            result.playerStatus || {},
                            result.enemyStatus || {},
                            result.playerRelics || []
                        );
                        this.remove();

                        if (result.guardianModeChanged) {
                            window.location.reload();
                            return;
                        }

                        if (result.isEnemyDead && result.redirect) {
                            window.location.href = result.redirect;
                        }
                    } else {
                        alert(result.message);
                    }
                } catch (error) {
                    console.error('Error playing card:', error);
                }
            });
        });

        function updateBattleState(playerHealth, enemyHealth, playerEnergy, playerBlock, enemyBlock, playerStatus = {}, enemyStatus = {}, playerRelics = []) {
            // Update health bars
            const playerHealthBar = document.querySelector('.player-health-bar');
            const playerHealthCurrent = document.querySelector('.player-health-value-current');
            const playerMaxHealth = <?php echo $_SESSION["playerMaxHealth"]; ?>;

            playerHealthBar.style.width = (playerHealth / playerMaxHealth * 100) + '%';
            playerHealthCurrent.textContent = playerHealth;

            // Update enemy health
            const enemyHealthBar = document.querySelector('.enemy-health-bar');
            const enemyHealthCurrent = document.querySelector('.enemy-health-value-current');
            const enemyMaxHealth = <?php echo $enemy->getMaxHealth(); ?>;

            enemyHealthBar.style.width = (enemyHealth / enemyMaxHealth * 100) + '%';
            enemyHealthCurrent.textContent = enemyHealth;

            // Update energy
            const energyValue = document.querySelector('.energy-value');
            energyValue.textContent = playerEnergy;

            // Update player block
            const playerBlockDiv = document.querySelector('.player-block');
            if (playerBlock > 0) {
                playerBlockDiv.innerHTML = `<div class="block-value">${playerBlock}</div>`;
            } else {
                playerBlockDiv.innerHTML = '';
            }

            // Update enemy block
            const enemyBlockDiv = document.querySelector('.enemy-block');
            if (enemyBlock > 0) {
                enemyBlockDiv.innerHTML = `<div class="block-value">${enemyBlock}</div>`;
            } else {
                enemyBlockDiv.innerHTML = '';
            }

            // Update player status icons
            if (Object.keys(playerStatus).length > 0) {
                updateStatusIcons('player-status', playerStatus);
            }

            // Update enemy status icons
            if (Object.keys(enemyStatus).length > 0) {
                updateStatusIcons('enemy-status', enemyStatus);
            }

            // Update relic counters if available
            if (playerRelics.length > 0) {
                updateRelicCounters(playerRelics);
            }
        }

        function updateStatusIcons(containerId, statusData) {
            const container = document.querySelector(`.${containerId}`);
            container.innerHTML = ''; // Clear existing status icons

            for (const [status, value] of Object.entries(statusData)) {
                if (value > 0) {
                    // Create a new status icon div
                    const iconDiv = document.createElement('div');
                    iconDiv.className = 'icon';
                    iconDiv.title = `${capitalizeFirstLetter(status.replace('_', ' '))}: ${value}`;

                    // Map internal status names to CSS class names
                    let cssClass = status;
                    if (status === 'curl_up') cssClass = 'closeUp';
                    if (status === 'spore_cloud') cssClass = 'sporeCloud';
                    if (status === 'enrage') cssClass = 'anger';

                    iconDiv.innerHTML = `
                        <div class="status-${cssClass}"></div>
                        <span class="status-value">${value}</span>
                    `;

                    container.appendChild(iconDiv);
                }
            }
        }

        function updateRelicCounters(relicsData) {
            const relicsContainer = document.querySelector('.player-relics');

            // Get all the relic icons
            const relicIcons = relicsContainer.querySelectorAll('.relic-icon');

            // Create a map of relic IDs to their counter data
            const relicMap = {};
            relicsData.forEach(relic => {
                relicMap[relic.id] = relic;
            });

            // Update each relic icon with the new counter value
            relicIcons.forEach(relicIcon => {
                const relicId = relicIcon.dataset.relicId;
                if (relicId && relicMap[relicId]) {
                    const relic = relicMap[relicId];

                    // Look for the counter div or create it if needed
                    let counterDiv = relicIcon.querySelector('.relic-counter');

                    if (relic.counter > 0) {
                        // Update or create counter
                        if (!counterDiv) {
                            counterDiv = document.createElement('div');
                            counterDiv.className = 'relic-counter';
                            relicIcon.appendChild(counterDiv);
                        }
                        counterDiv.textContent = relic.counter;
                    } else if (counterDiv) {
                        // Remove counter div if it exists and counter is 0 or negative
                        counterDiv.remove();
                    }

                    // Also update the title with the latest description
                    relicIcon.title = `${relic.name}: ${relic.description}`;
                }
            });
        }

        function capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }

        document.getElementById('endTurnBtn').addEventListener('click', async function() {
            try {
                const response = await fetch('game/endTurn', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const result = await response.json();

                if (result.success) {
                    // Update game state
                    updateBattleState(
                        result.playerHealth,
                        result.enemyHealth,
                        result.playerEnergy,
                        result.playerBlock,
                        result.enemyBlock,
                        result.playerStatus || {},
                        result.enemyStatus || {},
                        result.playerRelics || []
                    );

                    // Show enemy action message
                    if (result.enemyMoveMessage) {
                        alert(result.enemyMoveMessage);
                    }

                    // Remove all cards from hand
                    document.querySelectorAll('.card').forEach(card => card.remove());

                    // Update enemy intent
                    const intentDescription = document.querySelector('.intent-description');
                    intentDescription.textContent = result.moveDescription;

                    if (result.guardianModeChanged) {
                        window.location.reload();
                        return;
                    }

                    // Check if player or enemy is dead
                    if (result.isPlayerDead && result.redirect) {
                        window.location.href = result.redirect;
                        return;
                    } else if (result.isEnemyDead && result.redirect) {
                        window.location.href = result.redirect;
                        return;
                    } else {
                        // Instead of reloading the page, fetch new cards
                        window.location.reload(); // For now, still reload but in future can update just the cards
                    }
                } else {
                    alert(result.message || "Unknown error occurred");
                }
            } catch (error) {
                console.error('Error ending turn:', error);
                alert("Failed to end turn: " + error.message);
            }
        });

        function updateEnemyIntent(nextMove, description) {
            const intentIcon = document.querySelector('.intent-icon img');
            const intentDesc = document.querySelector('.intent-description');

            intentIcon.src = `static/images/intent/${nextMove}.png`;
            intentDesc.textContent = description;
        }
    });
    </script>

</head>
<body>
<?php require_once "header-game.php"; ?>
    <div>
        <div class="player">
            <div class="player-relics">
                <?php if (
                    isset($_SESSION["player_relics"]) &&
                    is_array($_SESSION["player_relics"])
                ) {
                    foreach ($_SESSION["player_relics"] as $relic) { ?>
                    <div class="relic-icon" data-relic-id="<?php echo $relic[
                        "id"
                    ]; ?>" title="<?php echo $relic["name"] .
    ": " .
    $relic["description"]; ?>">
                        <img src="<?php echo $relic[
                            "image"
                        ]; ?>" alt="<?php echo $relic["name"]; ?>">
                        <?php if ($relic["counter"] > 0) { ?>
                            <div class="relic-counter"><?php echo $relic[
                                "counter"
                            ]; ?></div>
                        <?php } ?>
                    </div>
                <?php }
                } ?>
            </div>
            <img src="static/images/ironclad.png" alt="Player" class="player-image">
            <div class="player-block">
                <?php if ($_SESSION["playerBlock"] > 0): ?>
                    <div class="block-value"><?php echo $_SESSION[
                        "playerBlock"
                    ]; ?></div>
                <?php endif; ?>
            </div>
            <div class="player-health">
                <div class="player-health-bar" style="width: <?php echo ($_SESSION[
                    "playerHealth"
                ] /
                    $_SESSION["playerMaxHealth"]) *
                    100; ?>%"></div>
                <div class="player-health-value">
                    <span class="player-health-value-current"><?php echo $_SESSION[
                        "playerHealth"
                    ]; ?></span>
                    <span>/</span>
                    <span class="player-health-max"><?php echo $_SESSION[
                        "playerMaxHealth"
                    ]; ?></span>
                </div>
            </div>
            <div class="player-energy">
                <div class="energy-orb">
                    <span class="energy-value"><?php echo $_SESSION[
                        "playerEnergy"
                    ]; ?></span>
                    <span class="energy-separator">/</span>
                    <span class="energy-max"><?php echo $_SESSION[
                        "playerMaxEnergy"
                    ]; ?></span>
                </div>
            </div>
            <div class="player-status">
                <?php foreach ($player->getStatus() as $status => $value): ?>
                    <div class="icon" title="<?php echo ucfirst($status) .
                        ": " .
                        $value; ?>">
                        <?php
                        // Map some internal status names to CSS class names
                        $cssClass = $status;
                        if ($status == "curl_up") {
                            $cssClass = "closeUp";
                        }
                        if ($status == "spore_cloud") {
                            $cssClass = "sporeCloud";
                        }
                        if ($status == "enrage") {
                            $cssClass = "anger";
                        }
                        ?>
                        <div class="status-<?php echo $cssClass; ?>"></div>
                        <span class="status-value"><?php echo $value; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        <div class="enemy">
            <!-- Use getter methods -->
            <img src="<?php echo $enemy->getSprite(); ?>" alt="<?php echo $enemy->getName(); ?>" class="enemy-image">
            <div class="enemy-block">
                <?php if ($enemy->getBlock() > 0): ?>
                    <div class="block-value"><?php echo $enemy->getBlock(); ?></div>
                <?php endif; ?>
            </div>
            <div class="enemy-health">
                <div class="enemy-health-bar" style="width: <?php echo ($enemy->getHealth() /
                    $enemy->getMaxHealth()) *
                    100; ?>%"></div>
                <div class="enemy-health-value">
                    <span class="enemy-health-value-current"><?php echo $enemy->getHealth(); ?></span>
                    <span>/</span>
                    <span class="enemy-health-max"><?php echo $enemy->getMaxHealth(); ?></span>
                </div>
            </div>
            <div class="enemy-status">
                <?php foreach ($enemy->getStatus() as $status => $value): ?>
                    <div class="icon" title="<?php echo ucfirst(
                        str_replace("_", " ", $status)
                    ) .
                        ": " .
                        $value; ?>">
                        <?php
                        // Convert only special cases, most statuses use the same name for CSS
                        $cssClass = $status;
                        if ($status === "curl_up") {
                            $cssClass = "closeUp";
                        }
                        if ($status === "spore_cloud") {
                            $cssClass = "sporeCloud";
                        }
                        if ($status === "enrage") {
                            $cssClass = "anger";
                        }
                        ?>
                        <div class="status-<?php echo $cssClass; ?>"></div>
                        <span class="status-value"><?php echo $value; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="enemy-name"><?php echo $enemy->getName(); ?></div>
        </div>
        <div class="hand">
            <?php foreach ($player->getHand() as $card): ?>
                <div class="card" data-card-id="<?php echo $card->getId(); ?>">
                    <div class="card-cost"><?php echo $card->getEnergyCost(); ?></div>
                    <div class="card-name"><?php echo $card->getName(); ?></div>
                    <img src="<?php echo $card->getImage(); ?>" alt="<?php echo $card->getName(); ?>" class="card-image">
                    <div class="card-description"><?php echo $card->getDescription(); ?></div>
                    <div class="card-type"><?php echo $card->getType(); ?></div>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="enemy-intent">
            <div class="intent-icon">
                <?php
                $move = $enemy->getNextMove();
                if ($move === "bite") {
                    echo '<i class="fas fa-teeth"></i>';
                } elseif ($move === "grow") {
                    echo '<i class="fas fa-arrow-up"></i>';
                }
                ?>
            </div>
            <div class="intent-description"><?php echo $enemy->getMoveDescription(); ?></div>
        </div>

        <div class="end-turn-button">
            <button id="endTurnBtn">End Turn</button>
        </div>
    </div>
    <div class="background-2"></div>
    <div class="background"></div>
</body>
</html>
