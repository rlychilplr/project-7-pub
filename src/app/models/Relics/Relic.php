<?php

abstract class Relic
{
    protected string $id;
    protected string $name;
    protected string $description;
    protected string $image;
    protected string $rarity; // Common, Uncommon, Rare, Boss, Shop, Starter, Event
    protected bool $isActive = true;
    protected int $counter = -1; // Some relics use counters, -1 means no counter

    public function __construct(
        string $id,
        string $name,
        string $description,
        string $image,
        string $rarity
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->image = $image;
        $this->rarity = $rarity;
    }

    // Getters
    public function getId(): string
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }
    public function getDescription(): string
    {
        return $this->description;
    }
    public function getImage(): string
    {
        return $this->image;
    }
    public function getRarity(): string
    {
        return $this->rarity;
    }
    public function isActive(): bool
    {
        return $this->isActive;
    }
    public function getCounter(): int
    {
        return $this->counter;
    }

    public function setCounter(int $value): void
    {
        $this->counter = $value;
    }
    public function incrementCounter(int $amount = 1): void
    {
        $this->counter += $amount;
    }
    public function decrementCounter(int $amount = 1): void
    {
        $this->counter -= $amount;
    }

    // Methods that can be overridden by specific relics
    public function onBattleStart($player): void {}
    public function onTurnStart($player): void {}
    public function onTurnEnd($player): void {}
    public function onCardPlayed($player, $card): void {}
    public function onAttackPlayed($player, $card, $target): void {}
    public function onSkillPlayed($player, $card): void {}
    public function onPowerPlayed($player, $card): void {}
    public function onDamageDealt($player, int $damage): int
    {
        return $damage;
    }
    public function onDamageTaken($player, int $damage): int
    {
        return $damage;
    }
    public function onBlockGained($player, int $blockAmount): int
    {
        return $blockAmount;
    }
    public function onEnemyDeath($player, $enemy): void {}
    public function onVictory($player): void {}
}
