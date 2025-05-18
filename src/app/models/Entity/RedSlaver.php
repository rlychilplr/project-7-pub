<?php

require_once "Enemy.php";

class RedSlaver extends Enemy
{
    public function __construct()
    {
        $maxHealth = rand(46, 52);
        parent::__construct(
            $maxHealth,
            $maxHealth,
            "Red Slaver",
            "static/images/enemies/regular/red_slaver.png"
        );
        $this->moves = ["Stab", "Scrape"];
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
            case "Stab":
                $damage = 9 + $this->getStatusValue("strength");
                $target->takeDamage($damage);
                $moveMessage = "Red Slaver stabs for {$damage} damage!";
                break;
            case "Scrape":
                $damage = 7 + $this->getStatusValue("strength");
                $target->takeDamage($damage);
                $target->addStatus("vulnerable", 2);
                $moveMessage = "Red Slaver scrapes you for {$damage} damage and applies 2 Vulnerable!";
                break;
        }

        $this->selectNextMove(); // Select next move for the next turn
        return $moveMessage;
    }

    public function getMoveDescription(): string
    {
        switch ($this->getNextMove()) {
            case "Stab":
                $baseDamage = 9;
                $strength = $this->getStatusValue("strength");
                $totalDamage = $baseDamage + $strength;
                return "Intends to deal {$totalDamage} damage ({$baseDamage} + {$strength})";
            case "Scrape":
                $baseDamage = 7;
                $strength = $this->getStatusValue("strength");
                $totalDamage = $baseDamage + $strength;
                return "Intends to deal {$totalDamage} damage and apply 2 Vulnerable ({$baseDamage} + {$strength})";
            default:
                return "Unknown intent";
        }
    }
}
