<?php

require_once "Enemy.php";

class AcidSlimeM extends Enemy
{
    public function __construct()
    {
        $maxHealth = rand(28, 34);
        parent::__construct(
            $maxHealth,
            $maxHealth,
            "acidSlimeM",
            "static/images/enemies/regular/acid_slime_m.png"
        );
        $this->moves = ["Tackle", "Ascension"];
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
            case "Tackle":
                $damage = 7 + $this->getStatusValue("strength");
                $target->takeDamage($damage);
                $moveMessage = "Acid Slime tackles for {$damage} damage!";
                break;
            case "Ascension":
                $damage = 6 + $this->getStatusValue("strength");
                $target->takeDamage($damage);
                $target->addStatus("weak", 2); // Apply 2 weak to the player
                $moveMessage = "Acid Slime spits acid for {$damage} damage and applies 2 Weak!";
                break;
        }

        $this->selectNextMove(); // Select next move for the next turn
        return $moveMessage;
    }

    public function getMoveDescription(): string
    {
        switch ($this->getNextMove()) {
            case "Tackle":
                $damage = 7 + $this->getStatusValue("strength");
                return "Intends to deal {$damage} damage";
            case "Ascension":
                $damage = 6 + $this->getStatusValue("strength");
                return "Intends to deal {$damage} damage and apply 2 Weak";
            default:
                return "Unknown intent";
        }
    }
}