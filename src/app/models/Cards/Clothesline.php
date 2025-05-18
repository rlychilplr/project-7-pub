<?php
require_once __DIR__ . "/Card.php";

class Clothesline extends Card
{
    public function __construct()
    {
        parent::__construct(
            "clothesline",
            "Clothesline",
            "Deal 12 damage. Apply 2 Weak.",
            2,
            "Attack",
            "Common",
            "static/images/temp/images/1024Portraits/red/attack/clothesline.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(12);
        $target->takeDamage($damage);
        $target->addStatus("weak", 2);
    }

    public function playWithPenNib(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(12, false, false, true);
        $target->takeDamage($damage);
        $target->addStatus("weak", 2);
    }
}
