<?php

require_once __DIR__ . "/../Entity/Player.php";
require_once __DIR__ . "/../Entity/Enemy.php";

abstract class Card
{
    protected string $id;
    protected string $name;
    protected string $description;
    protected int $energyCost;
    protected string $type; // Attack, Skill, Power, etc.
    protected string $rarity; // Starter, Common, Uncommon, Rare
    protected string $image;

    public function __construct(
        string $id,
        string $name,
        string $description,
        int $energyCost,
        string $type,
        string $rarity,
        string $image
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->energyCost = $energyCost;
        $this->type = $type;
        $this->rarity = $rarity;
        $this->image = $image;
    }

    abstract public function play(Player $player, Enemy $target): void;
    public function playWithPenNib(Player $player, Enemy $target): void
    {
        // Default implementation falls back to regular play
        // Each attack card can override this if needed
        $this->play($player, $target);
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
    public function getEnergyCost(): int
    {
        return $this->energyCost;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getRarity(): string
    {
        return $this->rarity;
    }
    public function getImage(): string
    {
        return $this->image;
    }
}
