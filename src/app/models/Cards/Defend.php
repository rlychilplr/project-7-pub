<?php
require_once __DIR__ . "/Card.php";

class Defend extends Card
{
    public function __construct()
    {
        parent::__construct(
            "defend",
            "Defend",
            "Gain 5 Block.",
            1,
            "Skill",
            "Starter",
            "static/images/temp/images/1024Portraits/red/skill/defend.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        $player->addBlock(5);
    }
}
