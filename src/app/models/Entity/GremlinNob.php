<?php

require_once "Enemy.php";

class GremlinNob extends Enemy
{
    private $hasUsedBellow = false;

    public function __construct()
    {
        $maxHealth = rand(82, 86);
        parent::__construct(
            $maxHealth,
            $maxHealth,
            "Gremlin Nob",
            "static/images/enemies/elite/gremlin_nob.png"
        );

        $this->moves = ["Bellow", "Rush", "Skull Bash"];

        // Load state from session if available
        if (isset($_SESSION["nob_has_used_bellow"])) {
            $this->hasUsedBellow = $_SESSION["nob_has_used_bellow"];
        }

        // Select first move if not already set
        if (!isset($_SESSION["enemy_next_move"])) {
            $this->selectNextMove();
        }
    }

    public function selectNextMove(): void
    {
        // First move is always Bellow if it hasn't been used yet
        if (!$this->hasUsedBellow) {
            $this->setNextMove("Bellow");
            return;
        }

        // After Bellow has been used, randomly select between Rush and Skull Bash
        $availableMoves = ["Rush", "Skull Bash"];

        // Avoid repeating the same move if possible
        $previousMove = $this->getNextMove();
        if (in_array($previousMove, $availableMoves)) {
            $filtered = array_filter($availableMoves, function ($move) use (
                $previousMove
            ) {
                return $move !== $previousMove;
            });

            // If we have moves remaining after filtering, use them
            if (!empty($filtered)) {
                $availableMoves = $filtered;
            }
        }

        $selectedMove = $availableMoves[array_rand($availableMoves)];
        $this->setNextMove($selectedMove);
    }

    public function executeMove(Player $target): string
    {
        $moveMessage = "";
        $currentMove = $this->getNextMove();

        switch ($currentMove) {
            case "Bellow":
                // Add 2 Enrage status
                $this->addStatus("enrage", 2);
                $moveMessage = "Gremlin Nob bellows loudly, gaining 2 Enrage!";
                $this->hasUsedBellow = true;
                $_SESSION["nob_has_used_bellow"] = true;
                break;

            case "Rush":
                $baseDamage = 14;
                $strength = $this->getStatusValue("strength");
                $damage = $baseDamage + $strength;
                $target->takeDamage($damage);
                $moveMessage = "Gremlin Nob rushes at you for {$damage} damage! (Base: {$baseDamage}, Strength: +{$strength})";
                break;

            case "Skull Bash":
                $baseDamage = 6;
                $strength = $this->getStatusValue("strength");
                $damage = $baseDamage + $strength;
                $target->takeDamage($damage);
                $target->addStatus("vulnerable", 2);
                $moveMessage = "Gremlin Nob bashes you with its skull for {$damage} damage and applies 2 Vulnerable! (Base: {$baseDamage}, Strength: +{$strength})";
                break;
        }

        // Select next move
        $this->selectNextMove();
        return $moveMessage;
    }

    public function getMoveDescription(): string
    {
        switch ($this->getNextMove()) {
            case "Bellow":
                return "Intends to gain 2 Enrage";

            case "Rush":
                $baseDamage = 14;
                $strength = $this->getStatusValue("strength");
                $totalDamage = $baseDamage + $strength;
                return "Intends to deal {$totalDamage} damage ({$baseDamage} + {$strength})";

            case "Skull Bash":
                $baseDamage = 6;
                $strength = $this->getStatusValue("strength");
                $totalDamage = $baseDamage + $strength;
                return "Intends to deal {$totalDamage} damage and apply 2 Vulnerable ({$baseDamage} + {$strength})";

            default:
                return "Unknown intent";
        }
    }

    // Handle the Enrage mechanic - when player plays a Skill card
    public function onPlayerSkillPlayed(Player $player): void
    {
        if ($this->hasStatus("enrage")) {
            $enrageAmount = $this->getStatusValue("enrage");
            $this->addStatus("strength", $enrageAmount);

            // Update session with the new status
            if (isset($_SESSION["enemyStatus"])) {
                $_SESSION["enemyStatus"] = $this->getStatus();
            }
        }
    }

    // Override the parent method to clean up session variables
    public function cleanupSession(): void
    {
        parent::cleanupSession();
        unset($_SESSION["nob_has_used_bellow"]);
    }
}
