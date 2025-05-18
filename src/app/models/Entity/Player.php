<?php

require_once "Entity.php";
require_once __DIR__ . "/../Cards/Strike.php";
require_once __DIR__ . "/../Cards/Defend.php";
require_once __DIR__ . "/../Cards/Bash.php";
require_once __DIR__ . "/../Relics/Relic.php";

class Player extends Entity
{
    private array $deck;
    private array $hand;
    private array $discardPile;
    private array $drawPile;
    private int $energy;
    private int $maxEnergy;
    private array $relics = [];

    public function __construct(array $characterData)
    {
        parent::__construct(
            $characterData["health"],
            $characterData["max_health"],
            $characterData["name"],
            $characterData["sprite"]
        );

        // Initialize basic properties
        $this->deck = [];
        $this->hand = [];
        $this->discardPile = [];
        $this->drawPile = [];
        $this->energy = $_SESSION["playerEnergy"];
        $this->maxEnergy = $_SESSION["playerMaxEnergy"];
        $this->block = $_SESSION["playerBlock"] ?? 0;
        $this->status = [];

        if (
            isset($_SESSION["playerStatus"]) &&
            is_array($_SESSION["playerStatus"])
        ) {
            $this->status = $_SESSION["playerStatus"];
        }

        $this->relics = []; // Reset relics array

        // Load relics from session ONCE - don't duplicate them
        if (
            isset($_SESSION["player_relics"]) &&
            is_array($_SESSION["player_relics"])
        ) {
            foreach ($_SESSION["player_relics"] as $relicData) {
                $relicClass = $this->getRelicClass($relicData["id"]);
                if ($relicClass) {
                    // Create the relic instance
                    $relic = new $relicClass();

                    // If there's a counter value, restore it
                    if (
                        isset($relicData["counter"]) &&
                        $relicData["counter"] >= 0
                    ) {
                        $relic->setCounter($relicData["counter"]);
                    }

                    // Add to relic collection
                    $this->relics[] = $relic;
                }
            }
        }

        // Initialize deck if it's a new game
        if (!isset($_SESSION["player_deck"])) {
            $this->initializeStarterDeck();
        } else {
            // Load the complete deck (reference collection)
            $this->deck = $this->deserializeCards($_SESSION["player_deck"]);
        }

        // Load discard pile from session if it exists
        if (
            isset($_SESSION["player_discard_pile"]) &&
            is_array($_SESSION["player_discard_pile"])
        ) {
            $this->discardPile = $this->deserializeCards(
                $_SESSION["player_discard_pile"]
            );
        }

        // Load draw pile from session if it exists
        if (
            isset($_SESSION["player_draw_pile"]) &&
            is_array($_SESSION["player_draw_pile"])
        ) {
            $this->drawPile = $this->deserializeCards(
                $_SESSION["player_draw_pile"]
            );
        }

        // If the draw pile is empty and we've already loaded cards into the discard pile,
        // shuffle the discard pile into the draw pile
        if (empty($this->drawPile) && !empty($this->discardPile)) {
            $this->reshuffleDiscardIntoDraw();
        }

        // CRITICAL: If both piles are empty after trying to reshuffle,
        // this suggests all cards are missing, so recreate from deck
        if (
            empty($this->drawPile) &&
            empty($this->discardPile) &&
            !empty($this->deck)
        ) {
            // Create a fresh draw pile with all cards from the deck
            $this->drawPile = array_map(function ($card) {
                $class = get_class($card);
                return new $class();
            }, $this->deck);
            shuffle($this->drawPile);
            $_SESSION["player_draw_pile"] = $this->serializeCards(
                $this->drawPile
            );
        }

        // Restore or draw new hand
        if (
            isset($_SESSION["player_hand"]) &&
            is_array($_SESSION["player_hand"]) &&
            !empty($_SESSION["player_hand"])
        ) {
            $this->hand = $this->deserializeCards($_SESSION["player_hand"]);
        } else {
            $this->drawCard(5);
        }
    }

    public function drawCard(int $count = 1): void
    {
        for ($i = 0; $i < $count; $i++) {
            // If draw pile is empty, try to reshuffle discard pile
            if (count($this->drawPile) === 0) {
                if (count($this->discardPile) === 0) {
                    // Emergency card recovery
                    if (!empty($this->deck)) {
                        $this->drawPile = array_map(function ($card) {
                            $class = get_class($card);
                            return new $class();
                        }, $this->deck);
                        shuffle($this->drawPile);
                        $_SESSION["player_draw_pile"] = $this->serializeCards(
                            $this->drawPile
                        );
                    }
                    break; // Break out of the loop if we can't draw cards
                } else {
                    $this->reshuffleDiscardIntoDraw();
                }
            }

            if (count($this->drawPile) > 0) {
                $drawnCard = array_pop($this->drawPile);
                $this->hand[] = $drawnCard;
            } else {
                break;
            }
        }

        // Update session
        $_SESSION["player_hand"] = $this->serializeCards($this->hand);
        $_SESSION["player_draw_pile"] = $this->serializeCards($this->drawPile);
    }

    public function playCard(string $cardId, Enemy $target): array
    {
        try {
            // Find the card in hand
            $cardIndex = false;

            for ($i = 0; $i < count($this->hand); $i++) {
                if ($this->hand[$i]->getId() === $cardId) {
                    $cardIndex = $i;
                    break;
                }
            }

            if ($cardIndex === false) {
                throw new Exception("Card not in hand. Card ID: " . $cardId);
            }

            $card = $this->hand[$cardIndex];

            // Check if we have enough energy
            if ($this->energy < $card->getEnergyCost()) {
                throw new Exception("Not enough energy");
            }

            // If this is an attack card, notify the enemy
            if ($card->getType() === "Attack") {
                $target->onPlayerAttack($this, 1); // Passing 1 as default number of attacks
            }

            // Check for Pen Nib before playing the card
            $penNibActive = false;
            if ($card->getType() === "Attack") {
                foreach ($this->relics as $relic) {
                    if ($relic instanceof PenNib) {
                        if ($relic->getCounter() === 9) {
                            $penNibActive = true;
                        } else {
                        }
                        break;
                    }
                }
            }

            // Pass penNibActive to the card when appropriate
            if ($penNibActive) {
                $card->playWithPenNib($this, $target);
            } else {
                $card->play($this, $target);
            }

            // Check if enemy died and trigger death effects
            if ($target->getHealth() <= 0) {
                $target->triggerDeathEffects($this);
            }

            // Process relic effects after card is played
            foreach ($this->relics as $relic) {
                $relic->onCardPlayed($this, $card);

                // Additional specific triggers based on card type
                if ($card->getType() === "Attack") {
                    $relic->onAttackPlayed($this, $card, $target);
                } elseif ($card->getType() === "Skill") {
                    $relic->onSkillPlayed($this, $card);
                } elseif ($card->getType() === "Power") {
                    $relic->onPowerPlayed($this, $card);
                }
            }

            // Remove the card from hand and add to discard pile
            $removedCard = array_splice($this->hand, $cardIndex, 1)[0];
            $this->discardPile[] = $removedCard;

            // Update session
            $_SESSION["player_hand"] = $this->serializeCards($this->hand);
            $_SESSION["player_discard_pile"] = $this->serializeCards(
                $this->discardPile
            );
            $_SESSION["player_draw_pile"] = $this->serializeCards(
                $this->drawPile
            );

            // Use energy
            $this->useEnergy($card->getEnergyCost());

            return [
                "message" => $card->getName() . " was played",
            ];
        } catch (Throwable $e) {
            throw $e;
        }
    }

    public function discardHand(): void
    {
        try {
            // Add all cards from hand to discard pile
            foreach ($this->hand as $card) {
                $this->discardPile[] = $card;
            }

            // Clear the hand
            $this->hand = [];

            // Update session
            $_SESSION["player_hand"] = [];
            $_SESSION["player_discard_pile"] = $this->serializeCards(
                $this->discardPile
            );
            // Also ensure draw pile is properly saved
            $_SESSION["player_draw_pile"] = $this->serializeCards(
                $this->drawPile
            );
        } catch (Throwable $e) {
            error_log("Error in discardHand: " . $e->getMessage());
            throw $e;
        }
    }

    public function getDrawPileCount(): int
    {
        return count($this->drawPile);
    }

    public function getDiscardPileCount(): int
    {
        return count($this->discardPile);
    }

    /**
     * Debug function to dump card distribution
     */
    public function debugCardState(): string
    {
        $state = "Card state:\n";
        $state .= "Draw pile (" . count($this->drawPile) . "):\n";
        foreach ($this->drawPile as $card) {
            $state .= "  - " . $card->getName() . "\n";
        }

        $state .= "Hand (" . count($this->hand) . "):\n";
        foreach ($this->hand as $card) {
            $state .= "  - " . $card->getName() . "\n";
        }

        $state .= "Discard pile (" . count($this->discardPile) . "):\n";
        foreach ($this->discardPile as $card) {
            $state .= "  - " . $card->getName() . "\n";
        }

        return $state;
    }
    /**
     * @return array The draw pile
     */
    public function getDrawPile(): array
    {
        return $this->drawPile;
    }

    private function reshuffleDiscardIntoDraw(): void
    {
        $this->drawPile = $this->discardPile;
        $this->discardPile = [];
        shuffle($this->drawPile);

        $_SESSION["player_draw_pile"] = $this->serializeCards($this->drawPile);
        $_SESSION["player_discard_pile"] = [];
    }

    private function initializeStarterDeck(): void
    {
        // Add 5 Strikes
        for ($i = 0; $i < 5; $i++) {
            $this->deck[] = new Strike();
        }
        // Add 4 Defends
        for ($i = 0; $i < 4; $i++) {
            $this->deck[] = new Defend();
        }
        // Add 1 Bash
        $this->deck[] = new Bash();

        // Store deck in session
        $_SESSION["player_deck"] = $this->serializeCards($this->deck);

        // Initialize the draw pile with a copy of the deck and shuffle it
        $this->drawPile = array_map(function ($card) {
            $class = get_class($card);
            return new $class();
        }, $this->deck);
        shuffle($this->drawPile);

        $_SESSION["player_draw_pile"] = $this->serializeCards($this->drawPile);
    }

    private function serializeCards(array $cards): array
    {
        try {
            return array_map(function ($card) {
                return [
                    "type" => get_class($card),
                    "id" => $card->getId(),
                ];
            }, $cards);
        } catch (Throwable $e) {
            error_log("Error in serializeCards: " . $e->getMessage());
            return []; // Return empty array as fallback
        }
    }

    private function deserializeCards(array $serializedCards): array
    {
        try {
            if (empty($serializedCards)) {
                return [];
            }

            $cards = [];
            foreach ($serializedCards as $cardData) {
                if (!isset($cardData["type"]) || !isset($cardData["id"])) {
                    continue;
                }

                $cardClass = $cardData["type"];
                if (!class_exists($cardClass)) {
                    continue; // Skip this card but continue with others
                }
                $cards[] = new $cardClass();
            }
            return $cards;
        } catch (Throwable $e) {
            error_log("Error in deserializeCards: " . $e->getMessage());
            return []; // Return empty array as fallback
        }
    }

    public function getDiscardPile(): array
    {
        return $this->discardPile;
    }

    public function getHand(): array
    {
        return $this->hand;
    }

    public function useEnergy(int $amount): void
    {
        $this->energy = max(0, $this->energy - $amount);
        $_SESSION["playerEnergy"] = $this->energy;
    }

    public function resetEnergy(): void
    {
        $this->energy = $this->maxEnergy;
        $_SESSION["playerEnergy"] = $this->energy;
    }

    public function getEnergy(): int
    {
        return $this->energy;
    }

    public function addBlock(int $amount, bool $applyDexterity = true): void
    {
        // Apply dexterity effect if enabled
        if ($applyDexterity && $this->getStatusValue("dexterity") > 0) {
            $dexterity = $this->getStatusValue("dexterity");
            $amount += $dexterity;
        }

        if ($this->hasStatus("frail")) {
            $amount = (int) ($amount * 0.75);
        }

        $this->block += $amount;
    }

    /**
     * @param Card $card The card to add to the discard pile
     */
    public function addToDiscardPile($card): void
    {
        $this->discardPile[] = $card;

        // Update the session
        $_SESSION["player_discard_pile"] = $this->serializeCards(
            $this->discardPile
        );
    }

    // Add a method to add relics
    public function addRelic(Relic $relic): void
    {
        $this->relics[] = $relic;

        // If we're adding a relic mid-battle, trigger its onBattleStart effect
        if (isset($_SESSION["enemyHealth"])) {
            $relic->onBattleStart($this);
        }
    }

    // Get all equipped relics
    public function getRelics(): array
    {
        return $this->relics;
    }

    public function battleStart($applyEffects = true): void
    {
        // Skip if we're not supposed to apply effects
        if (!$applyEffects) {
            return;
        }

        // Only apply battle start effects once per battle
        if (
            !isset($_SESSION["battle_start_effects_applied"]) ||
            $_SESSION["battle_start_effects_applied"] !== true
        ) {
            // Apply relic effects
            foreach ($this->relics as $relic) {
                $relic->onBattleStart($this);
            }

            // IMPORTANT: Store the updated status in session after applying effects
            $_SESSION["playerStatus"] = $this->status;
            $_SESSION["battle_start_effects_applied"] = true;
        }
    }

    public function turnStart(): void
    {
        foreach ($this->relics as $relic) {
            $relic->onTurnStart($this);
        }
    }

    public function turnEnd(): void
    {
        foreach ($this->relics as $relic) {
            $relic->onTurnEnd($this);
        }
    }

    public function takeDamage(int $damage): void
    {
        // Apply relic modifications to incoming damage
        foreach ($this->relics as $relic) {
            $damage = $relic->onDamageTaken($this, $damage);
        }

        parent::takeDamage($damage);
    }

    public function victory(): void
    {
        foreach ($this->relics as $relic) {
            $relic->onVictory($this);
        }
    }

    public function calculateDamage(
        int $baseDamage,
        bool $isWeak = false,
        bool $isVulnerable = false,
        bool $hasPenNib = false,
        bool $hasPaperPhrog = false,
        int $targetBlock = 0,
        int $targetDamageReduction = 0
    ): int {
        $damage = parent::calculateDamage(
            $baseDamage,
            $isWeak,
            $isVulnerable,
            $hasPenNib,
            $hasPaperPhrog,
            $targetBlock,
            $targetDamageReduction
        );

        // Apply relic modifications to outgoing damage
        foreach ($this->relics as $relic) {
            $damage = $relic->onDamageDealt($this, $damage);
        }

        return $damage;
    }

    public function endTurn(): void
    {
        // Reduce duration of temporary statuses
        $this->decrementStatusDurations();

        // Trigger relic end turn effects
        foreach ($this->relics as $relic) {
            $relic->onTurnEnd($this);
        }
    }

    private function decrementStatusDurations(): void
    {
        // Status effects that naturally decay at end of turn
        $decayingStatuses = ["weak", "vulnerable", "frail"];

        foreach ($decayingStatuses as $status) {
            if ($this->hasStatus($status)) {
                $currentValue = $this->getStatusValue($status);
                if ($currentValue > 1) {
                    $this->status[$status] = $currentValue - 1;
                }
            }
        }
    }

    private function getRelicClass(string $relicId): ?string
    {
        $relicMap = [
            "burning_blood" => "BurningBlood",
            "akabeko" => "Akabeko",
            "oddly_smooth_stone" => "OddlySmoothStone",
            "pen_nib" => "PenNib",
            "vajra" => "Vajra",
        ];

        if (isset($relicMap[$relicId])) {
            return $relicMap[$relicId];
        }

        return null;
    }

    public function resetBattleStartFlag(): void
    {
        // Directly unset the session variable to reset the flag
        unset($_SESSION["battle_start_effects_applied"]);
    }

    public function setStatus(array $statusArray): void
    {
        $this->status = $statusArray;
    }
}
