<script>
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');

    cards.forEach(card => {
        card.addEventListener('click', async function() {
            const cardId = this.dataset.cardId;

            try {
                const response = await fetch('game/playCard', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ cardId: cardId })
                });

                const result = await response.json();

                if (result.success) {
                    // Update health bars
                    updateHealthBars(result.playerHealth, result.enemyHealth);
                    // Remove the played card
                    this.remove();
                } else {
                    alert(result.message);
                }
            } catch (error) {
                console.error('Error playing card:', error);
            }
        });
    });

    function updateHealthBars(playerHealth, enemyHealth) {
        // Update player health
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
    }
});

</script>
