<?php
require_once __DIR__ . "/Card.php";

class Strike extends Card
{
    public function __construct()
    {
        parent::__construct(
            "strike",
            "Strike",
            "Deal 6 damage.",
            1,
            "Attack",
            "Starter",
            "static/images/temp/images/1024Portraits/red/attack/strike.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(6);
        $target->takeDamage($damage);
    }

    public function playWithPenNib(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(6, false, false, true);
        $target->takeDamage($damage);
    }
}
