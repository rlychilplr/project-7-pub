<?php
require_once "Relic.php";

class Vajra extends Relic
{
    public function __construct()
    {
        parent::__construct(
            "vajra",
            "Vajra",
            "At the start of each combat, gain 1 Strength.",
            "static/images/temp/images/largeRelics/vajra.png",
            "Common"
        );
    }

    public function onBattleStart($player): void
    {
        // Log the current strength before applying the effect
        $currentStrength = $player->getStatusValue("strength");

        // Add 1 strength
        $player->addStatus("strength", 1);
    }
}
