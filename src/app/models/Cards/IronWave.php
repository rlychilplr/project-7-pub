<?php
require_once __DIR__ . "/Card.php";

class IronWave extends Card
{
    public function __construct()
    {
        parent::__construct(
            "iron_wave",
            "Iron Wave",
            "Deal 5 damage. Gain 5 Block.",
            1,
            "Attack",
            "Common",
            "static/images/temp/images/1024Portraits/red/attack/iron_wave.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(5);
        $target->takeDamage($damage);
        $player->addBlock(5);
    }

    public function playWithPenNib(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(5, false, false, true);
        $target->takeDamage($damage);
        $player->addBlock(5);
    }
}
