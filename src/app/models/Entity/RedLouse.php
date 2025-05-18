<?php

require_once "Enemy.php";

class RedLouse extends Enemy
{
    public function __construct()
    {
        $maxHealth = rand(10, 16);
        parent::__construct(
            $maxHealth,
            $maxHealth,
            "Red Louse",
            "static/images/enemies/regular/red_louse.png"
        );
        $this->moves = ["grow", "bite"];

        // Load existing status from session if available
        if (
            isset($_SESSION["enemyStatus"]) &&
            is_array($_SESSION["enemyStatus"])
        ) {
            $this->status = $_SESSION["enemyStatus"];
        }

        // The problem is that we're checking for $_SESSION["enemyHealth"]
        // which might be set when we're creating this enemy, but we don't
        // want to reset curl_up if it's already been removed

        // Only set curl_up if it's a new battle OR we need to restore it
        if (
            (!isset($_SESSION["enemyHealth"]) ||
                (isset($_SESSION["enemy_type"]) &&
                    $_SESSION["enemy_type"] == 5)) &&
            !isset($this->status["curl_up"]) &&
            !isset($_SESSION["curl_up_used"]) // We'll use this to track if it's been used
        ) {
            $curlUpAmount = rand(3, 7);
            $this->status["curl_up"] = $curlUpAmount;
            $_SESSION["enemyStatus"] = $this->status;
        }

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
                $baseDamage = 5;
                $strength = $this->getStatusValue("strength");
                $damage = $baseDamage + $strength;
                $target->takeDamage($damage);
                $moveMessage = "Red Louse bites for {$damage} damage! (Base: {$baseDamage}, Strength: +{$strength})";
                break;
            case "grow":
                $this->addStatus("strength", 2);
                $moveMessage = "Red Louse curls up! (+2 Strength)";
                break;
        }

        $this->selectNextMove(); // Select next move for the next turn
        return $moveMessage;
    }

    public function getMoveDescription(): string
    {
        switch ($this->getNextMove()) {
            case "bite":
                $baseDamage = 5;
                $strength = $this->getStatusValue("strength");
                $totalDamage = $baseDamage + $strength;
                return "Intends to deal {$totalDamage} damage ({$baseDamage} + {$strength})";
            case "grow":
                return "Intends to curl up (+2 Strength)";
            default:
                return "Unknown intent";
        }
    }
}
