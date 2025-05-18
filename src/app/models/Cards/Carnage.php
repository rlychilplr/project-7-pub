<?php
require_once __DIR__ . "/Card.php";

class Carnage extends Card
{
    public function __construct()
    {
        parent::__construct(
            "carnage",
            "Carnage",
            "Deal 20 damage.",
            2,
            "Attack",
            "Uncommon",
            "static/images/temp/images/1024Portraits/red/attack/carnage.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(20);
        $target->takeDamage($damage);
    }

    public function playWithPenNib(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(20, false, false, true);
        $target->takeDamage($damage);
    }
}
