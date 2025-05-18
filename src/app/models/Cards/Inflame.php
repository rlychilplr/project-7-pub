<?php
require_once __DIR__ . "/Card.php";

class Inflame extends Card
{
    public function __construct()
    {
        parent::__construct(
            "inflame",
            "Inflame",
            "Gain 2 Strength.",
            1,
            "Power",
            "Uncommon",
            "static/images/temp/images/1024Portraits/red/power/inflame.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        $player->addStatus("strength", 2);
    }
}
