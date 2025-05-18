<?php

require_once "Enemy.php";

class Cultist extends Enemy
{
    public function __construct()
    {
        $maxHealth = rand(48, 56);
        parent::__construct(
            $maxHealth,
            $maxHealth,
            "Cultist", // Fixed the name from "Fungi Beast"
            "static/images/enemies/regular/cultist.png"
        );
        $this->moves = ["Incantation", "Dark Strike"];
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
            case "Dark Strike":
                $baseDamage = 6;
                $strength = $this->getStatusValue("strength");
                $damage = $baseDamage + $strength;
                $target->takeDamage($damage);
                $moveMessage = "Cultist strikes for {$damage} damage! (Base: {$baseDamage}, Strength: +{$strength})";
                break;
            case "Incantation":
                $this->addStatus("ritual", 3);
                $moveMessage = "Cultist performs a dark ritual! (+3 Ritual)";
                // Important: We don't apply the ritual effect immediately when getting the ritual status
                break;
        }

        $this->selectNextMove(); // Select next move for the next turn

        // DO NOT apply ritual effect here - it happens at the beginning of enemy's turn
        // $this->applyRitualEffect(); - Remove this line

        return $moveMessage;
    }

    public function getMoveDescription(): string
    {
        switch ($this->getNextMove()) {
            case "Dark Strike":
                $baseDamage = 6;
                $strength = $this->getStatusValue("strength");
                $totalDamage = $baseDamage + $strength;
                return "Intends to deal {$totalDamage} damage ({$baseDamage} + {$strength})";
            case "Incantation":
                return "Intends to perform a dark ritual (+3 Ritual)";
            default:
                return "Unknown intent";
        }
    }
}
