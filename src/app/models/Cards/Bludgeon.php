<?php
require_once __DIR__ . "/Card.php";

class Bludgeon extends Card
{
    public function __construct()
    {
        parent::__construct(
            "bludgeon",
            "Bludgeon",
            "Deal 32 damage.",
            3,
            "Attack",
            "Rare",
            "static/images/temp/images/1024Portraits/red/attack/bludgeon.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(32);
        $target->takeDamage($damage);
    }

    public function playWithPenNib(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(32, false, false, true);
        $target->takeDamage($damage);
    }
}
