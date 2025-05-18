<?php
require_once __DIR__ . "/Card.php";

class Anger extends Card
{
    public function __construct()
    {
        parent::__construct(
            "anger",
            "Anger",
            "Deal 6 damage, Add a copy of this card to your discard pile. ",
            0,
            "Attack",
            "Common",
            "static/images/temp/images/1024Portraits/red/attack/anger.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(6);
        $target->takeDamage($damage);

        $newAnger = new Anger();
        $player->addToDiscardPile($newAnger);
    }

    public function playWithPenNib(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(6, false, false, true);
        $target->takeDamage($damage);

        $newAnger = new Anger();
        $player->addToDiscardPile($newAnger);
    }
}
