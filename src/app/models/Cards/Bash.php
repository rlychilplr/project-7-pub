<?php
require_once __DIR__ . "/Card.php";

class Bash extends Card
{
    public function __construct()
    {
        parent::__construct(
            "bash",
            "Bash",
            "Deal 8 damage. Apply 2 Vulnerable.",
            2,
            "Attack",
            "Starter",
            "static/images/temp/images/1024Portraits/red/attack/bash.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        try {
            $damage = $player->calculateDamage(8);
            $target->takeDamage($damage);
            $target->addStatus("vulnerable", 2);
        } catch (Throwable $e) {
            error_log("Error in Bash::play: " . $e->getMessage());
            throw $e;
        }
    }

    public function playWithPenNib(Player $player, Enemy $target): void
    {
        $damage = $player->calculateDamage(8, false, false, true);
        $target->takeDamage($damage);
        $target->addStatus("vulnerable", 2);
    }
}
