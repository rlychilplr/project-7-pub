<?php
require_once "Relic.php";

class OddlySmoothStone extends Relic
{
    public function __construct()
    {
        parent::__construct(
            "oddly_smooth_stone",
            "Oddly Smooth Stone",
            "At the start of each combat, gain 1 Dexterity.",
            "static/images/temp/images/largeRelics/smooth_stone.png",
            "Common"
        );
    }

    public function onBattleStart($player): void
    {
        // Log the current dexterity before applying the effect
        $currentDexterity = $player->getStatusValue("dexterity");

        // Add 1 dexterity
        $player->addStatus("dexterity", 1);
    }
}
