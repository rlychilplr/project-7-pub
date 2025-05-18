<?php

require_once "Entity.php";

abstract class Enemy extends Entity
{
    protected array $moves;
    protected string $nextMove;

    public function __construct(
        int $health,
        int $maxHealth,
        string $name,
        string $sprite
    ) {
        parent::__construct($health, $maxHealth, $name, $sprite);
        $this->moves = [];
        $this->nextMove = "";
        $this->status = [];
    }

    abstract public function selectNextMove(): void;
    abstract public function executeMove(Player $target): string;
    abstract public function getMoveDescription(): string;

    public function getNextMove(): string
    {
        return isset($_SESSION["enemy_next_move"])
            ? $_SESSION["enemy_next_move"]
            : $this->nextMove;
    }

    protected function setNextMove(string $move): void
    {
        $this->nextMove = $move;
        $_SESSION["enemy_next_move"] = $move;
    }

    public function addStatus(string $status, int $amount): void
    {
        parent::addStatus($status, $amount);
        $_SESSION["enemyStatus"] = $this->status;
    }

    public function removeStatus(string $status): void
    {
        parent::removeStatus($status);
        $_SESSION["enemyStatus"] = $this->status;
    }

    public function endTurn(): void
    {
        $this->decrementStatusDurations();
        if (!$this->hasStatus("retain_block")) {
            $this->block = 0;
        }
    }

    private function decrementStatusDurations(): void
    {
        $decayingStatuses = ["weak", "vulnerable", "frail"];

        foreach ($decayingStatuses as $status) {
            if ($this->hasStatus($status)) {
                $currentValue = $this->getStatusValue($status);
                if ($currentValue > 1) {
                    $this->status[$status] = $currentValue - 1;
                } else {
                    $this->removeStatus($status);
                }
            }
        }
    }

    public function setStatus(array $statusArray): void
    {
        $this->status = $statusArray;
    }

    public function getStatus(): array
    {
        // Make sure session is updated before returning
        if (isset($_SESSION["enemyStatus"])) {
            $_SESSION["enemyStatus"] = $this->status;
        }
        return $this->status;
    }

    public function onPlayerAttack(Player $player, int $attacks = 1): void
    {
        // Handle any effects that trigger when player attacks
        if ($this->hasStatus("thorns")) {
            $this->takeThornsRetaliation($player, $attacks);
        }
    }

    // Method to handle thorns damage
    public function takeThornsRetaliation(Player $player, int $attacks): void
    {
        if ($this->hasStatus("thorns")) {
            $thornsDamage = $this->getStatusValue("thorns") * $attacks;
            $player->takeDamage($thornsDamage);
        }
    }
    public function cleanupSession(): void {}
}
