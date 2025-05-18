<?php

abstract class Entity
{
    protected int $health;
    protected int $maxHealth;
    protected array $status;
    protected string $name;
    protected string $sprite;
    protected int $block = 0;

    public function __construct(
        int $health,
        int $maxHealth,
        string $name,
        string $sprite
    ) {
        $this->health = $health;
        $this->maxHealth = $maxHealth;
        $this->name = $name;
        $this->sprite = $sprite;
        $this->status = [];
    }

    public function getHealth(): int
    {
        return $this->health;
    }

    public function getMaxHealth(): int
    {
        return $this->maxHealth;
    }

    public function takeDamage(int $damage): void
    {
        // Only trigger Curl Up when actually taking damage (not blocked)
        $actualDamage = $damage;

        // Apply block first
        if ($this->block > 0) {
            if ($damage <= $this->block) {
                $this->block -= $damage;
                return; // No actual damage taken, don't trigger Curl Up
            } else {
                $actualDamage = $damage - $this->block;
                $this->block = 0;
            }
        }

        // If we're going to take actual damage and have Curl Up, apply it before the damage
        if ($actualDamage > 0 && $this->hasStatus("curl_up")) {
            $curlUpAmount = $this->getStatusValue("curl_up");
            $this->addBlock($curlUpAmount);

            // Remove the curl_up status
            unset($this->status["curl_up"]);

            // Set a flag to track that curl_up has been used
            $_SESSION["curl_up_used"] = true;

            // Ensure the session is updated to remove curl_up
            if (isset($_SESSION["enemyStatus"])) {
                unset($_SESSION["enemyStatus"]["curl_up"]);
            }

            // Recalculate damage after applying Curl Up
            if ($this->block > 0) {
                if ($actualDamage <= $this->block) {
                    $this->block -= $actualDamage;
                    return;
                } else {
                    $actualDamage = $actualDamage - $this->block;
                    $this->block = 0;
                }
            }
        }

        // Apply remaining damage to health
        $this->health = max(0, $this->health - $actualDamage);
    }

    public function heal(int $healing): void
    {
        $this->health = min($this->maxHealth, $this->health + $healing);
    }

    public function addStatus(string $status, int $amount): void
    {
        // Skip if amount is zero
        if ($amount == 0) {
            return;
        }

        $oldValue = $this->status[$status] ?? 0;
        // Add to existing value instead of setting it
        $newValue = $oldValue + $amount;

        if ($newValue <= 0) {
            // If the new value would be zero or negative, remove the status
            unset($this->status[$status]);
        } else {
            // Otherwise set the new value
            $this->status[$status] = $newValue;
        }

        // Update session immediately if this is the player
        if ($this instanceof Player) {
            $_SESSION["playerStatus"] = $this->status;
        }
    }

    public function setStatusValue(string $status, int $amount): void
    {
        if ($amount <= 0) {
            $this->removeStatus($status);
            return;
        }

        $oldValue = $this->status[$status] ?? 0;
        $this->status[$status] = $amount;
    }

    public function removeStatus(string $status): void
    {
        unset($this->status[$status]);
    }

    public function hasStatus(string $status): bool
    {
        return isset($this->status[$status]) && $this->status[$status] > 0;
    }

    public function getStatusValue(string $status): int
    {
        return $this->status[$status] ?? 0;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSprite(): string
    {
        return $this->sprite;
    }

    public function getStatus(): array
    {
        return $this->status;
    }

    public function setHealth(int $health): void
    {
        $this->health = $health;
    }

    public function setMaxHealth(int $maxHealth): void
    {
        $this->maxHealth = $maxHealth;
    }

    public function addBlock(int $amount): void
    {
        if ($this->hasStatus("frail")) {
            $amount = (int) ($amount * 0.75);
        }

        $this->block += $amount;
    }

    public function getBlock(): int
    {
        return $this->block;
    }

    public function calculateDamage(
        int $baseDamage,
        bool $isWeak = false,
        bool $isVulnerable = false,
        bool $hasPenNib = false
    ): int {
        // Original damage calculation
        $damage = $baseDamage + $this->getStatusValue("strength");

        // Apply Weak status effect
        if ($isWeak || $this->hasStatus("weak")) {
            $oldDamage = $damage;
            $damage = (int) ($damage * 0.75);
        }

        // Apply Vulnerable status effect
        if ($isVulnerable || $this->hasStatus("vulnerable")) {
            $oldDamage = $damage;
            $damage = (int) ($damage * 1.5);
        }

        // Apply Pen Nib if specified
        if ($hasPenNib) {
            $oldDamage = $damage;
            $damage = $damage * 2;
        }

        return max(0, $damage);
    }

    public function applyRitualEffect(): void
    {
        if ($this->hasStatus("ritual")) {
            $ritualAmount = $this->getStatusValue("ritual");
            $currentStrength = $this->getStatusValue("strength");
            $this->status["strength"] = $currentStrength + $ritualAmount;
        }
    }

    public function triggerDeathEffects(Entity $attacker): void
    {
        if ($this->hasStatus("spore_cloud") && $this->health <= 0) {
            $vulnAmount = $this->getStatusValue("spore_cloud");
            $attacker->addStatus("vulnerable", $vulnAmount);
        }
    }

    public function setStatus(array $statusArray): void
    {
        $this->status = $statusArray;
    }
}
