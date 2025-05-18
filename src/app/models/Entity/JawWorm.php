<?php

require_once "Enemy.php";

class JawWorm extends Enemy
{
    public function __construct()
    {
        $maxHealth = rand(40, 46);
        parent::__construct(
            $maxHealth,
            $maxHealth,
            "Jaw Worm",
            "static/images/enemies/regular/jaw_worm.png"
        );
        $this->moves = ["Thrash", "Chomp", "Bellow"];
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
            case "Chomp":
                $baseDamage = 11;
                $strength = $this->getStatusValue("strength");
                $damage = $baseDamage + $strength;
                $target->takeDamage($damage);
                $moveMessage = "Jaw Worm chomps for {$damage} damage! (Base: {$baseDamage}, Strength: +{$strength})";
                break;
            case "Thrash":
                $baseDamage = 7;
                $strength = $this->getStatusValue("strength");
                $damage = $baseDamage + $strength;
                $target->takeDamage($damage);
                $this->addBlock(5);
                $moveMessage = "Jaw Worm thrashes for {$damage} damage and gains 5 Block! (Base: {$baseDamage}, Strength: +{$strength})";
                break;
            case "Bellow":
                $strength = $this->getStatusValue("strength");
                $this->addBlock(6);
                $this->addStatus("strength", 3);
                $moveMessage = "Jaw Worm gains +{$this->getBlock()} Block and +{$strength} Strength";
                break;
        }

        $this->selectNextMove(); // Select next move for the next turn
        return $moveMessage;
    }

    public function getMoveDescription(): string
    {
        switch ($this->getNextMove()) {
            case "Chomp":
                $baseDamage = 11;
                $strength = $this->getStatusValue("strength");
                $totalDamage = $baseDamage + $strength;
                return "Intends to deal {$totalDamage} damage ({$baseDamage} + {$strength})";
            case "Thrash":
                $baseDamage = 7;
                $strength = $this->getStatusValue("strength");
                $totalDamage = $baseDamage + $strength;
                return "Intends to deal {$totalDamage} damage and gain 5 block ({$baseDamage} + {$strength})";
            case "Bellow":
                return "Intends to gain 6 Block and 3 Strength";
            default:
                return "Unknown intent";
        }
    }
}
