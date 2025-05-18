<?php
require_once "Relic.php";

class PenNib extends Relic
{
    public function __construct()
    {
        parent::__construct(
            "pen_nib",
            "Pen Nib",
            "Every 10th Attack deals double damage.",
            "static/images/temp/images/largeRelics/penNib.png",
            "Common"
        );
        $this->counter = 0; // Start at 0
    }

    public function onBattleStart($player): void
    {
        // Counter persists between battles, no need to reset
        $this->updateCounterInSession();
    }

    public function onAttackPlayed($player, $card, $target): void
    {
        if ($card->getType() === "Attack") {
            // Check if this attack is the 10th attack (counter is 9)
            if ($this->counter == 9) {
            }

            // Increment counter for each attack played
            $this->counter++;

            // If this is the 10th attack (counter = 10), reset to 0
            if ($this->counter >= 10) {
                $this->counter = 0;
            }

            $this->updateCounterInSession();
        }
    }

    private function updateCounterInSession(): void
    {
        if (isset($_SESSION["player_relics"])) {
            foreach ($_SESSION["player_relics"] as &$relic) {
                if ($relic["id"] === "pen_nib") {
                    $relic["counter"] = $this->counter;
                    break;
                }
            }
        }
    }

    public function onDamageDealt($player, int $damage): int
    {
        // Double damage when counter is exactly 9 (about to be the 10th attack)
        if ($this->counter == 9) {
            $doubledDamage = $damage * 2;

            return $doubledDamage;
        } else {
            return $damage;
        }
    }
}
