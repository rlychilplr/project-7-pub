<?php
require_once __DIR__ . "/Card.php";

class TwinStrike extends Card
{
    public function __construct()
    {
        parent::__construct(
            "twin_strike",
            "Twin Strike",
            "Deal 5 damage twice.",
            1,
            "Attack",
            "Common",
            "static/images/temp/images/1024Portraits/red/attack/twin_strike.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(5);
        $target->takeDamage($damage);
        $target->takeDamage($damage);
    }

    public function playWithPenNib(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(5, false, false, true);
        $target->takeDamage($damage);
        $target->takeDamage($damage);
    }
}
