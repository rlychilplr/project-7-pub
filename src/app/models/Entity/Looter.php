<?php

require_once "Enemy.php";

class Looter extends Enemy
{
    public function __construct()
    {
        $maxHealth = rand(48, 52);
        parent::__construct(
            $maxHealth,
            $maxHealth,
            "Looter",
            "static/images/enemies/regular/looter.png"
        );
        $this->moves = ["Mug", "Lunge"];
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
            case "Mug":
                $damage = 10 + $this->getStatusValue("strength");
                $target->takeDamage($damage);
                $moveMessage = "Looter mugs you for {$damage} damage!";
                break;
            case "Lunge":
                $damage = 12 + $this->getStatusValue("strength");
                $target->takeDamage($damage);
                $this->addStatus("strength", 2);
                $moveMessage = "Looter lunges for {$damage} damage and gains 2 strength!";
                break;
        }

        $this->selectNextMove(); // Select next move for the next turn
        return $moveMessage;
    }

    public function getMoveDescription(): string
    {
        switch ($this->getNextMove()) {
            case "Mug":
                $damage = 10 + $this->getStatusValue("strength");
                return "Intends to deal {$damage} damage";
            case "Lunge":
                $damage = 12 + $this->getStatusValue("strength");
                return "Intends to deal {$damage} damage and gain 2 strength";
            default:
                return "Unknown intent";
        }
    }
}