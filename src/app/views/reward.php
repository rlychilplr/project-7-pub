<?php
/**
 * @var array $cards List of card objects to choose from
 */
?>
<!DOCTYPE html>
<html>
<head>
    <title>Victory Rewards | Slay the Spire Recreation</title>
    <link rel="stylesheet" href="static/css/font.css">
    <link rel="stylesheet" href="static/css/reward.css">
</head>
<body>
    <div class="reward-container">
        <h1>Victory!</h1>

        <?php if (isset($eliteRelicData)): ?>
        <div class="elite-relic-reward">
            <h2>Elite Reward: Relic Obtained!</h2>
            <div class="relic-container">
                <img src="<?php echo $eliteRelicData[
                    "image"
                ]; ?>" alt="<?php echo $eliteRelicData[
    "name"
]; ?>" class="relic-image">
                <div class="relic-name"><?php echo $eliteRelicData[
                    "name"
                ]; ?></div>
                <div class="relic-description"><?php echo $eliteRelicData[
                    "description"
                ]; ?></div>
            </div>
        </div>
        <?php endif; ?>

        <p>Choose a card to add to your deck:</p>

        <div class="cards-container">
            <?php foreach ($cards as $card): ?>
                <div class="card" data-card-id="<?php echo $card->getId(); ?>">
                    <div class="card-cost"><?php echo $card->getEnergyCost(); ?></div>
                    <div class="card-name"><?php echo $card->getName(); ?></div>
                    <img src="<?php echo $card->getImage(); ?>" alt="<?php echo $card->getName(); ?>" class="card-image">
                    <div class="card-description"><?php echo $card->getDescription(); ?></div>
                    <div class="card-type"><?php echo $card->getType(); ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <button id="skipBtn">Skip</button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event to cards
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.addEventListener('click', async function() {
                    const cardId = this.dataset.cardId;
                    console.log('Selected card:', cardId);

                    try {
                        const response = await fetch('reward', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({ cardId: cardId })
                        });

                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }

                        const result = await response.json();

                        if (result.success && result.redirect) {
                            window.location.href = result.redirect;
                        } else {
                            alert('Error selecting card: ' + (result.message || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Error selecting card:', error);
                        alert('Failed to select card: ' + error.message);
                    }
                });
            });

            // Add click event to skip button
            document.getElementById('skipBtn').addEventListener('click', function() {
                window.location.href = 'map';
            });
        });
    </script>
    <div class="background-2"></div>
    <div class="background"></div>
</body>
</html>
