<?php
require_once __DIR__ . "/Card.php";

class BodySlam extends Card
{
    public function __construct()
    {
        parent::__construct(
            "body_slam",
            "Body Slam",
            "Deal damage equal to your current Block. ",
            1,
            "Attack",
            "Common",
            "static/images/temp/images/1024Portraits/red/attack/body_slam.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage($player->getBlock());
        $target->takeDamage($damage);
    }

    public function playWithPenNib(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(
            $player->getBlock(),
            false,
            false,
            true
        );
        $target->takeDamage($damage);
    }
}
