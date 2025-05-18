<?php

require_once "Enemy.php";

class BlueSlaver extends Enemy
{
    public function __construct()
    {
        $maxHealth = rand(46, 52);
        parent::__construct(
            $maxHealth,
            $maxHealth,
            "Blue Slaver",
            "static/images/enemies/regular/blue_slaver.png"
        );
        $this->moves = ["rake", "stab"];
        if (!isset($_SESSION["enemy_next_move"])) {
            $this->selectNextMove();
        }
    }

    public function selectNextMove(): void
    {
        $move = $this->moves[array_rand($this->moves)];
        $this->setNextMove($move);
    }

    public function executeMove(Player $target): string
    {
        $moveMessage = "";
        $currentMove = $this->getNextMove();

        switch ($currentMove) {
            case "rake":
                $baseDamage = 7;
                $strength = $this->getStatusValue("strength");
                $damage = $baseDamage + $strength;
                $target->takeDamage($damage);
                $target->addStatus("weak", 1);
                $moveMessage = "Blue Slaver rakes for {$damage} damage and applies 1 Weak! (Base: {$baseDamage}, Strength: +{$strength})";
                break;
            case "stab":
                $baseDamage = 12;
                $strength = $this->getStatusValue("strength");
                $damage = $baseDamage + $strength;
                $target->takeDamage($damage);
                $moveMessage = "Blue Slaver stabs for {$damage} damage! (Base: {$baseDamage}, Strength: +{$strength})";
                break;
        }

        $this->selectNextMove(); // Select next move for the next turn
        return $moveMessage;
    }

    public function getMoveDescription(): string
    {
        switch ($this->getNextMove()) {
            case "rake":
                $baseDamage = 7;
                $strength = $this->getStatusValue("strength");
                $totalDamage = $baseDamage + $strength;
                return "Intends to deal {$totalDamage} damage and apply 1 Vulnerable ({$baseDamage} + {$strength})";
            case "stab":
                $baseDamage = 12;
                $strength = $this->getStatusValue("strength");
                $totalDamage = $baseDamage + $strength;
                return "Intends to deal {$totalDamage} damage ({$baseDamage} + {$strength})";
            default:
                return "Unknown intent";
        }
    }
}
