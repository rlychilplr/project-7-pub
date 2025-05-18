<?php
require_once __DIR__ . "/Card.php";

class HeavyBlade extends Card
{
    public function __construct()
    {
        parent::__construct(
            "heavy_blade",
            "Heavy Blade",
            "Deal 14 damage. Strength affects Heavy Blade 3 times. ",
            2,
            "Attack",
            "Common",
            "static/images/temp/images/1024Portraits/red/attack/heavy_blade.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        // Special case: strength affects this 3x
        $baseDamage = 14;
        $damage = $player->calculateDamage($baseDamage);
        $extraStrength = $player->getStatusValue("strength") * 2; // Add 2 more times
        $damage += $extraStrength;

        $target->takeDamage($damage);
    }

    public function playWithPenNib(Player $player, Enemy $target): void
    {
        $baseDamage = 14;
        $extraStrength = $player->getStatusValue("strength") * 2; // Add 2 more times
        $totalDamage =
            $player->calculateDamage($baseDamage, false, false, true) +
            $extraStrength;
        $target->takeDamage($totalDamage);
    }
}
