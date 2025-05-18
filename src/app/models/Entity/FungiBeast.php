<?php

require_once "Enemy.php";

class FungiBeast extends Enemy
{
    public function __construct()
    {
        $maxHealth = rand(22, 28);
        parent::__construct(
            $maxHealth,
            $maxHealth,
            "Fungi Beast",
            "static/images/enemies/regular/fungi_beast.png"
        );
        $this->moves = ["bite", "grow"];

        // Add Spore Cloud as a death effect
        $this->addStatus("spore_cloud", 2);

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
            case "bite":
                $baseDamage = 6;
                $strength = $this->getStatusValue("strength");
                $damage = $baseDamage + $strength;
                $target->takeDamage($damage);

                // Add a chance to apply vulnerable
                if (rand(1, 3) == 1) {
                    // 33% chance
                    $target->addStatus("vulnerable", 2);
                    $moveMessage = "Fungi Beast bites for {$damage} damage and applies 2 Vulnerable! (Base: {$baseDamage}, Strength: +{$strength})";
                } else {
                    $moveMessage = "Fungi Beast bites for {$damage} damage! (Base: {$baseDamage}, Strength: +{$strength})";
                }
                break;
            case "grow":
                $this->addStatus("strength", 3);
                $moveMessage = "Fungi Beast grows stronger! (+3 Strength)";
                break;
        }

        $this->selectNextMove(); // Select next move for the next turn
        return $moveMessage;
    }

    public function getMoveDescription(): string
    {
        switch ($this->getNextMove()) {
            case "bite":
                $baseDamage = 6;
                $strength = $this->getStatusValue("strength");
                $totalDamage = $baseDamage + $strength;
                return "Intends to deal {$totalDamage} damage ({$baseDamage} + {$strength})";
            case "grow":
                return "Intends to grow stronger (+3 Strength)";
            default:
                return "Unknown intent";
        }
    }
}
