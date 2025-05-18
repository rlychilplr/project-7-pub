<?php

require_once "Relic.php";

require_once "Relic.php";

class BurningBlood extends Relic
{
    public function __construct()
    {
        parent::__construct(
            "burning_blood",
            "Burning Blood",
            "At the end of combat, heal 6 HP.",
            "static/images/temp/images/largeRelics/burningBlood.png",
            "Starter"
        );
    }

    public function onVictory($player): void
    {
        $player->heal(6);
    }
}
