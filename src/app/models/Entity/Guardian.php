<?php

require_once "Enemy.php";

class Guardian extends Enemy
{
    private $modeShiftThreshold;
    private $modeShiftCurrentValue;
    private $isDefensiveMode = false;
    private $turnsInDefensiveMode = 0;
    private $possibleMoves = [
        "offensive" => [
            "charging_up",
            "fierce_bash",
            "vent_steam",
            "whirlwind",
        ],
        "defensive" => ["roll_attack", "twin_slam"], // Removed spike_protocol
    ];
    // Sprite paths for different modes
    private $offensiveSprite = "static/images/enemies/boss/The_guardian.png";
    private $defensiveSprite = "static/images/enemies/boss/The_guardian-def.png";

    public function __construct()
    {
        $maxHealth = 240; // Standard health for Act 1 boss

        // Always start with the offensive sprite by default
        $sprite = $this->offensiveSprite; // Default offensive sprite

        // Only use defensive sprite if explicitly in defensive mode from previous state
        if (
            isset($_SESSION["guardian_defensive_mode"]) &&
            $_SESSION["guardian_defensive_mode"] === true
        ) {
            $sprite = $this->defensiveSprite;
            $this->isDefensiveMode = true; // Set mode flag immediately
        }

        parent::__construct(
            $maxHealth,
            $maxHealth,
            "The Guardian",
            $sprite // Use the correct sprite immediately
        );

        // Set mode shift threshold
        $this->modeShiftThreshold = 30;

        // Initialize or load mode shift current value from session
        if (isset($_SESSION["guardian_mode_shift_value"])) {
            $this->modeShiftCurrentValue =
                $_SESSION["guardian_mode_shift_value"];
        } else {
            $this->modeShiftCurrentValue = 30;
        }

        // Load defensive mode state from session - already handled the sprite above
        if ($this->isDefensiveMode) {
            $this->turnsInDefensiveMode =
                $_SESSION["guardian_defensive_turns"] ?? 0;

            // We've already set isDefensiveMode=true above, now just add thorns if needed
            // Check session first to avoid duplicate thorns
            if (
                !isset($_SESSION["enemyStatus"]) ||
                !is_array($_SESSION["enemyStatus"]) ||
                !isset($_SESSION["enemyStatus"]["thorns"])
            ) {
                $this->addStatus("thorns", 3);
            }
        } else {
            // Only add initial modeShift status if not in defensive mode
            if (
                !isset($_SESSION["enemyStatus"]) ||
                !is_array($_SESSION["enemyStatus"]) ||
                !isset($_SESSION["enemyStatus"]["modeShift"])
            ) {
                $this->addStatus("modeShift", $this->modeShiftCurrentValue);
            }
        }

        // Select first move if not already set
        if (!isset($_SESSION["enemy_next_move"])) {
            $this->selectNextMove();
        }
    }

    public function takeDamage(int $damage): void
    {
        // Track damage for Mode Shift only if not in defensive mode
        if (!$this->isDefensiveMode) {
            $this->modeShiftCurrentValue -= $damage;
            // Store the updated value in session
            $_SESSION["guardian_mode_shift_value"] =
                $this->modeShiftCurrentValue;

            // Update mode_shift status to show remaining threshold
            $this->setStatusValue(
                "modeShift",
                max(0, $this->modeShiftCurrentValue)
            );

            // If threshold reached, enter defensive mode
            if ($this->modeShiftCurrentValue <= 0) {
                $this->enterDefensiveMode();
            }
        }

        // Apply normal damage logic
        parent::takeDamage($damage);
    }

    private function enterDefensiveMode(): void
    {
        // Only change to defensive mode if not already in it
        if (!$this->isDefensiveMode) {
            $this->isDefensiveMode = true;
            $this->turnsInDefensiveMode = 0;

            // Add thorns if not already present - ONLY ONCE
            if (!$this->hasStatus("thorns")) {
                $this->addStatus("thorns", 3);
            }

            $this->removeStatus("modeShift");

            // Change sprite to defensive mode
            $this->sprite = $this->defensiveSprite;

            // When entering defensive mode, clear any current block and add massive block
            $this->block = 0;
            $this->addBlock(20);

            // Force next move to be from defensive set
            $this->selectNextMove();

            // Save state to session
            $_SESSION["guardian_defensive_mode"] = true;
            $_SESSION["guardian_defensive_turns"] = 0;

            // Set a flag to indicate the mode has changed
            $_SESSION["guardian_mode_changed"] = true;
        }
    }

    private function exitDefensiveMode(): void
    {
        $this->isDefensiveMode = false;
        $this->modeShiftCurrentValue = $this->modeShiftThreshold;
        $_SESSION["guardian_mode_shift_value"] = $this->modeShiftThreshold;

        $this->addStatus("modeShift", $this->modeShiftThreshold);
        $this->removeStatus("thorns");

        // Change sprite back to offensive mode
        $this->sprite = $this->offensiveSprite;

        // Force next move to be from offensive set
        $this->selectNextMove();

        // Update session
        $_SESSION["guardian_defensive_mode"] = false;
        unset($_SESSION["guardian_defensive_turns"]);

        // Set a flag to trigger page reload for sprite update
        $_SESSION["guardian_mode_changed"] = true;
    }

    public function selectNextMove(): void
    {
        $moveSet = $this->isDefensiveMode
            ? $this->possibleMoves["defensive"]
            : $this->possibleMoves["offensive"];

        // Logic for move selection based on current state
        if ($this->isDefensiveMode) {
            // In defensive mode, alternate between the two moves
            $move =
                $this->turnsInDefensiveMode % 2 == 0
                    ? "roll_attack"
                    : "twin_slam";
        } else {
            // In offensive mode, select randomly but with some rules
            $previousMove = $this->getNextMove();

            // Filter out the previous move to avoid repetition
            $filteredMoves = array_filter($moveSet, function ($m) use (
                $previousMove
            ) {
                return $m != $previousMove;
            });

            // If filtering left us with moves, randomly select one
            if (!empty($filteredMoves)) {
                $move = $filteredMoves[array_rand($filteredMoves)];
            } else {
                // Fallback if all moves were filtered out somehow
                $move = $moveSet[array_rand($moveSet)];
            }
        }

        $this->setNextMove($move);
    }

    public function executeMove(Player $target): string
    {
        $moveMessage = "";
        $currentMove = $this->getNextMove();

        switch ($currentMove) {
            case "charging_up":
                $this->addBlock(9);
                $moveMessage = "The Guardian gains 9 block!";
                break;

            case "fierce_bash":
                $baseDamage = 32;
                $strength = $this->getStatusValue("strength");
                $damage = $baseDamage + $strength;
                $target->takeDamage($damage);
                $moveMessage = "The Guardian bashes you fiercely for {$damage} damage!";
                break;

            case "vent_steam":
                $target->addStatus("weak", 2);
                $target->addStatus("vulnerable", 2);
                $moveMessage =
                    "The Guardian vents steam, applying 2 Weak and 2 Vulnerable!";
                break;

            case "whirlwind":
                $baseDamage = 5;
                $strength = $this->getStatusValue("strength");
                $damage = $baseDamage + $strength;
                for ($i = 0; $i < 4; $i++) {
                    $target->takeDamage($damage);
                }
                $moveMessage =
                    "The Guardian whirlwinds for {$damage} damage 4 times, totaling " .
                    $damage * 4 .
                    " damage!";
                break;

            case "roll_attack":
                $baseDamage = 9;
                $strength = $this->getStatusValue("strength");
                $damage = $baseDamage + $strength;
                $target->takeDamage($damage);
                $this->addBlock(9);
                $moveMessage = "The Guardian rolls into you for {$damage} damage and gains 9 Block!";
                break;

            case "twin_slam":
                $baseDamage = 8;
                $strength = $this->getStatusValue("strength");
                $damage = $baseDamage + $strength;
                $target->takeDamage($damage);
                $target->takeDamage($damage);
                $moveMessage =
                    "The Guardian slams you twice for {$damage} damage each, totaling " .
                    $damage * 2 .
                    " damage!";
                break;
        }

        // Handle defensive mode turn counter
        if ($this->isDefensiveMode) {
            $this->turnsInDefensiveMode++;
            $_SESSION["guardian_defensive_turns"] = $this->turnsInDefensiveMode;

            // After 2 turns, exit defensive mode
            if ($this->turnsInDefensiveMode >= 2) {
                $this->exitDefensiveMode();
                $moveMessage .= " The Guardian opens back up!";
            }
        }

        // Select next move
        $this->selectNextMove();
        return $moveMessage;
    }

    public function getMoveDescription(): string
    {
        switch ($this->getNextMove()) {
            case "charging_up":
                return "Intends to gain 9 block";
            case "fierce_bash":
                $baseDamage = 32;
                $strength = $this->getStatusValue("strength");
                $totalDamage = $baseDamage + $strength;
                return "Intends to deal {$totalDamage} damage";
            case "vent_steam":
                return "Intends to apply 2 Weak and 2 Vulnerable";
            case "whirlwind":
                $baseDamage = 5;
                $strength = $this->getStatusValue("strength");
                $totalDamage = ($baseDamage + $strength) * 4;
                return "Intends to deal {$baseDamage} damage 4 times (total: {$totalDamage})";
            case "roll_attack":
                $baseDamage = 9;
                $strength = $this->getStatusValue("strength");
                $totalDamage = $baseDamage + $strength;
                return "Intends to deal {$totalDamage} damage and gain 9 Block";
            case "twin_slam":
                $baseDamage = 8;
                $strength = $this->getStatusValue("strength");
                $totalDamage = ($baseDamage + $strength) * 2;
                return "Intends to deal {$baseDamage} damage twice (total: {$totalDamage})";
            default:
                return "Unknown intent";
        }
    }

    public function endTurn(): void
    {
        // First call parent endTurn for standard status handling
        parent::endTurn();

        // No additional Guardian-specific end-turn effects needed
    }

    // Override to add thorns damage when player attacks with attack cards
    public function takeThornsRetaliation(Player $player, int $attacks): void
    {
        if ($this->hasStatus("thorns")) {
            $thornsDamage = $this->getStatusValue("thorns") * $attacks;
            $player->takeDamage($thornsDamage);
        }
    }

    // Making sure to update getSprite() to return the current sprite
    public function getSprite(): string
    {
        // Always return the correct sprite based on the current mode
        return $this->isDefensiveMode
            ? $this->defensiveSprite
            : $this->offensiveSprite;
    }

    // Override setStatus to properly handle mode shift value
    public function setStatus(array $statusArray): void
    {
        parent::setStatus($statusArray);

        // Special handling for modeShift
        if (isset($statusArray["modeShift"]) && !$this->isDefensiveMode) {
            $this->modeShiftCurrentValue = $statusArray["modeShift"];
            $_SESSION["guardian_mode_shift_value"] =
                $this->modeShiftCurrentValue;
        }
    }

    public function cleanupSession(): void
    {
        unset($_SESSION["guardian_mode_shift_value"]);
        unset($_SESSION["guardian_defensive_mode"]);
        unset($_SESSION["guardian_defensive_turns"]);
        unset($_SESSION["guardian_mode_changed"]);
    }
}
