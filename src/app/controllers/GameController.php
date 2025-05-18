<?php
require_once __DIR__ . "/../core/Controller.php";
require_once __DIR__ . "/../models/CharacterModel.php";
require_once __DIR__ . "/../models/Entity/Player.php";

// enemys
require_once __DIR__ . "/../models/Entity/FungiBeast.php";
require_once __DIR__ . "/../models/Entity/RedSlaver.php";
require_once __DIR__ . "/../models/Entity/acidSlimeM.php";
require_once __DIR__ . "/../models/Entity/BlueSlaver.php";
require_once __DIR__ . "/../models/Entity/RedLouse.php";
require_once __DIR__ . "/../models/Entity/JawWorm.php";
require_once __DIR__ . "/../models/Entity/Cultist.php";
require_once __DIR__ . "/../models/Entity/GremlinNob.php";
require_once __DIR__ . "/../models/Entity/Guardian.php";

// card rewards
require_once __DIR__ . "/../models/Cards/Anger.php";
require_once __DIR__ . "/../models/Cards/BodySlam.php";
require_once __DIR__ . "/../models/Cards/Clothesline.php";
require_once __DIR__ . "/../models/Cards/Flex.php";
require_once __DIR__ . "/../models/Cards/HeavyBlade.php";
require_once __DIR__ . "/../models/Cards/IronWave.php";
require_once __DIR__ . "/../models/Cards/TwinStrike.php";
require_once __DIR__ . "/../models/Cards/Carnage.php";
require_once __DIR__ . "/../models/Cards/Entrench.php";
require_once __DIR__ . "/../models/Cards/Inflame.php";
require_once __DIR__ . "/../models/Cards/Bludgeon.php";

// relics
require_once __DIR__ . "/../models/Relics/Relic.php";
require_once __DIR__ . "/../models/Relics/BurningBlood.php";
require_once __DIR__ . "/../models/Relics/Akabeko.php";
require_once __DIR__ . "/../models/Relics/OddlySmoothStone.php";
require_once __DIR__ . "/../models/Relics/PenNib.php";
require_once __DIR__ . "/../models/Relics/Vajra.php";

class GameController extends Controller
{
    private $characterModel;
    private $enemyPlayer;

    //  private  const pathConnections = [
    //     '1'=> ['6', '7'],
    //     '2'=>['4'],
    //     '3' =>['5'],
    //     '4'=> ['9'],
    //     '5'=> ['9'],
    //     '6'=> ['8'],
    //     '7'=> ['8'],
    //     '8'=> ['11'],
    //     '9'=> ['10'],
    //     '10'=> ['12', '13'],
    //     '11'=> ['13', '14'],
    //     '12'=> ['15'],
    //     '13'=> ['16', '17'],
    //     '14'=> ['18'],
    //     '15'=> ['19'],
    //     '16'=> ['19', '20'],
    //     '17'=> ['21'],
    //     '18'=> ['21'],
    //     '19'=> ['22'],
    //     '20'=> ['22', '23'],
    //     '21'=> ['23', '24'],
    //     '22'=> ['26'],
    //     '23'=> ['27'],
    //     '24'=> ['25'],
    //     '25'=> ['29'],
    //     '26'=> ['28'],
    //     '27'=> ['30'],
    //     '28'=> ['30'],
    //     '29'=> ['30']
    //  ];

    public function __construct()
    {
        $this->characterModel = new CharacterModel();

        // Check if this is explicitly a boss or elite encounter from the URL path
        $requestUri = $_SERVER["REQUEST_URI"];
        $isBossPath = strpos($requestUri, "/boss") !== false;
        $isElitePath = strpos($requestUri, "/elite") !== false;

        // Set appropriate session values based on path
        if ($isBossPath) {
            $_SESSION["enemy_type"] = "boss_guardian";
        } elseif ($isElitePath) {
            $_SESSION["enemy_type"] = "elite_nob";
        }

        // Check the session for enemy type
        if (isset($_SESSION["enemy_type"])) {
            if ($_SESSION["enemy_type"] === "boss_guardian") {
                $this->enemyPlayer = new Guardian();
            } elseif ($_SESSION["enemy_type"] === "elite_nob") {
                $this->enemyPlayer = new GremlinNob();
            } elseif (is_numeric($_SESSION["enemy_type"])) {
                // Regular numeric enemy types
                $number = $_SESSION["enemy_type"];

                switch ($number) {
                    case 1:
                        $this->enemyPlayer = new FungiBeast();
                        break;
                    case 2:
                        $this->enemyPlayer = new RedSlaver();
                        break;
                    case 3:
                        $this->enemyPlayer = new AcidSlimeM();
                        break;
                    case 4:
                        $this->enemyPlayer = new BlueSlaver();
                        break;
                    case 5:
                        $this->enemyPlayer = new RedLouse();
                        break;
                    case 6:
                        $this->enemyPlayer = new JawWorm();
                        break;
                    case 7:
                        $this->enemyPlayer = new Cultist();
                        break;
                    default:
                        $this->enemyPlayer = new FungiBeast(); // fallback
                }
            } else {
                // Unknown enemy type, default to a random regular enemy
                $number = random_int(1, 7);
                $_SESSION["enemy_type"] = $number;

                // Re-run the constructor with the new enemy type
                return $this->__construct();
            }
        } else {
            // No enemy type set, default to a random regular enemy
            $number = random_int(1, 7);
            $_SESSION["enemy_type"] = $number;

            // Re-run the constructor with the new enemy type
            return $this->__construct();
        }

        // Load enemy status from session if available
        if (
            isset($_SESSION["enemyStatus"]) &&
            is_array($_SESSION["enemyStatus"])
        ) {
            $this->enemyPlayer->setStatus($_SESSION["enemyStatus"]);
        }
    }

    public function index(): void
    {
        try {
            if (!isset($_SESSION["username"])) {
                $this->redirect("login");
                return;
            }
            $nodeid = $_GET["node_id"] ?? 0;
            $_SESSION["nodeid"] = $nodeid;
            $_SESSION["nodeCompleteet"] = 0;

            // Check if we're entering a new battle
            $newBattle = !isset($_SESSION["enemyHealth"]);

            // Get character data outside the session check
            $characterData = $this->characterModel->getCharacterDetails(1);
            if (!$characterData) {
                throw new Exception(
                    "Character data not found. Please ensure the characters table is populated."
                );
            }

            // Special case for new elite encounters
            if (
                isset($_SESSION["new_elite_encounter"]) &&
                $_SESSION["new_elite_encounter"] === true
            ) {
                // This is a new elite encounter, use the already initialized enemy
                unset($_SESSION["new_elite_encounter"]);
                $newBattle = true; // Treat as a new battle for battle start effects
            }

            // Special case for new boss encounters
            if (
                isset($_SESSION["new_boss_encounter"]) &&
                $_SESSION["new_boss_encounter"] === true
            ) {
                // This is a new boss encounter, use the already initialized enemy
                unset($_SESSION["new_boss_encounter"]);
                $newBattle = true; // Treat as a new battle for battle start effects
            }

            // Initialize session variables if they don't exist
            if (!isset($_SESSION["playerHealth"])) {
                $_SESSION["characterName"] = $characterData["name"];
                $_SESSION["playerHealth"] = $characterData["health"];
                $_SESSION["playerMaxHealth"] = $characterData["max_health"];
                $_SESSION["playerBlock"] = 0;
                $_SESSION["playerStatus"] = [];
                $_SESSION["money"] = 99;
                $_SESSION["floor"] = 1;
                $_SESSION["player_hand"] = null;
                $_SESSION["player_deck"] = null;
                $_SESSION["player_draw_pile"] = null;
                $_SESSION["player_discard_pile"] = [];
                $_SESSION["playerEnergy"] = 3;
                $_SESSION["playerMaxEnergy"] = 3;
                $_SESSION["start_time"] = time();
            }

            if (!isset($_SESSION["player_relics"])) {
                $this->obtainRelic("burning_blood", false);
            }

            // At this point, enemy health should be set for both regular enemies and bosses

            // Handle the case of a new enemy (coming from reward screen)
            if (!isset($_SESSION["enemyHealth"])) {
                $newEnemy = isset($_SESSION["playerHealth"]); // We have player health but no enemy health

                // Set up a fresh enemy
                $_SESSION["enemyStatus"] = [];
                $enemy = $this->enemyPlayer;
                $_SESSION["enemyHealth"] = $enemy->getHealth();
                $_SESSION["enemyMaxHealth"] = $enemy->getMaxHealth();

                // If this was a new enemy after defeating a previous one, increment floor
                if ($newEnemy) {
                    $_SESSION["floor"]++;
                }
            }

            // Check if player is dead
            if (
                isset($_SESSION["playerHealth"]) &&
                $_SESSION["playerHealth"] <= 0
            ) {
                $this->redirect("game/over");
                return;
            }

            // Create enemy with stored health values
            $enemy = $this->enemyPlayer;
            $enemy->setHealth($_SESSION["enemyHealth"]);
            $enemy->setMaxHealth($_SESSION["enemyMaxHealth"]);
            $_SESSION["enemyBlock"] = $enemy->getBlock();

            if (
                isset($_SESSION["enemyStatus"]) &&
                is_array($_SESSION["enemyStatus"])
            ) {
                $enemy->setStatus($_SESSION["enemyStatus"]);
            }

            // Create player instance with character data
            $player = new Player([
                "health" => $_SESSION["playerHealth"],
                "max_health" => $_SESSION["playerMaxHealth"],
                "name" => $_SESSION["characterName"],
                "sprite" => "static/images/ironclad.png",
            ]);

            // MAKE SURE player status is loaded correctly from session
            if (
                isset($_SESSION["playerStatus"]) &&
                is_array($_SESSION["playerStatus"])
            ) {
                $player->setStatus($_SESSION["playerStatus"]);
            }

            // Only apply battle start effects if this is a new battle
            if ($newBattle) {
                // Reset the battle start flag and then apply effects
                $player->resetBattleStartFlag();
                $player->battleStart(true);
            }

            $battleData = [
                "enemy" => $enemy,
                "player" => $player,
            ];

            $this->render("battle", $battleData);
        } catch (Exception $e) {
            error_log("Error in GameController::index(): " . $e->getMessage());
            $this->render("error", [
                "message" =>
                    "An error occurred while loading the game: " .
                    $e->getMessage(),
            ]);
        }
    }

    public function playCard(): void
    {
        try {
            if ($_SERVER["REQUEST_METHOD"] !== "POST") {
                throw new Exception("Invalid request method");
            }

            $requestBody = file_get_contents("php://input");

            $data = json_decode($requestBody, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                error_log("JSON decode error: " . json_last_error_msg());
            }

            $cardId = $data["cardId"] ?? null;

            if (!$cardId) {
                throw new Exception("No card ID provided");
            }

            // Get the current game state
            $characterData = [
                "health" => $_SESSION["playerHealth"],
                "max_health" => $_SESSION["playerMaxHealth"],
                "name" => $_SESSION["characterName"],
                "sprite" => "static/images/ironclad.png",
            ];

            $player = new Player($characterData);
            $enemy = $this->enemyPlayer;
            $enemy->setHealth($_SESSION["enemyHealth"]);
            $enemy->setMaxHealth($_SESSION["enemyMaxHealth"]);

            // Get the card before playing it
            $cardToPlay = null;
            foreach ($player->getHand() as $card) {
                if ($card->getId() === $cardId) {
                    $cardToPlay = $card;
                    break;
                }
            }

            if (!$cardToPlay) {
                throw new Exception("Card not found in hand");
            }

            // Check if this is a skill card being played against Gremlin Nob
            if (
                $cardToPlay->getType() === "Skill" &&
                $enemy instanceof GremlinNob
            ) {
                $enemy->onPlayerSkillPlayed($player);
            }

            // Play the card
            try {
                $result = $player->playCard($cardId, $enemy);
            } catch (Throwable $playError) {
                error_log(
                    "Exception in player->playCard: " . $playError->getMessage()
                );
                error_log("Stack trace: " . $playError->getTraceAsString());
                throw $playError;
            }

            // Check if enemy is dead
            $isEnemyDead = $enemy->getHealth() <= 0;

            // Update session with new state
            $_SESSION["enemyHealth"] = $enemy->getHealth();
            $_SESSION["playerHealth"] = $player->getHealth();
            $_SESSION["playerBlock"] = $player->getBlock();
            $_SESSION["playerEnergy"] = $player->getEnergy();

            // IMPORTANT: Save player status to session - this was missing
            $_SESSION["playerStatus"] = $player->getStatus();
            $_SESSION["enemyStatus"] = $enemy->getStatus();

            $response = [
                "success" => true,
                "enemyHealth" => $enemy->getHealth(),
                "playerHealth" => $player->getHealth(),
                "playerEnergy" => $_SESSION["playerEnergy"],
                "playerBlock" => $player->getBlock(),
                "enemyBlock" => $enemy->getBlock(),
                "playerStatus" => $player->getStatus(),
                "enemyStatus" => $enemy->getStatus(),
                "playerRelics" => $_SESSION["player_relics"] ?? [],
                "message" => $result["message"] ?? "Card played successfully",
                "isEnemyDead" => $isEnemyDead,
            ];

            // Check if the defeated enemy was the boss
            $defeatedBoss =
                $isEnemyDead &&
                isset($_SESSION["enemy_type"]) &&
                $_SESSION["enemy_type"] === "boss_guardian";

            if ($defeatedBoss) {
                $_SESSION["defeated_boss"] = true;
                $response["redirect"] = "victory";
            } elseif ($isEnemyDead) {
                $response["redirect"] = "reward";
                $_SESSION["nodeCompleted"] = 1;
            }

            if (
                isset($_SESSION["guardian_mode_changed"]) &&
                $_SESSION["guardian_mode_changed"] === true
            ) {
                $response["guardianModeChanged"] = true;
            }

            if ($isEnemyDead) {
                $response["redirect"] = "reward";
                $_SESSION["nodeCompleted"] = 1;
            }

            try {
                $this->json($response);
            } catch (Throwable $responseError) {
                error_log(
                    "Error sending response: " . $responseError->getMessage()
                );
                throw $responseError;
            }
        } catch (Throwable $e) {
            error_log(
                "Exception in GameController::playCard: " . $e->getMessage()
            );
            error_log("Stack trace: " . $e->getTraceAsString());

            // Try to send a simple error response
            try {
                header("Content-Type: application/json");
                echo json_encode([
                    "success" => false,
                    "message" => "Error: " . $e->getMessage(),
                ]);
            } catch (Throwable $finalError) {
                // At this point we've done all we can
                error_log(
                    "Failed to send error response: " .
                        $finalError->getMessage()
                );
            }
        }
    }

    public function endTurn(): void
    {
        try {
            // Get current game state
            $characterData = [
                "health" => $_SESSION["playerHealth"],
                "max_health" => $_SESSION["playerMaxHealth"],
                "name" => $_SESSION["characterName"],
                "sprite" => "static/images/ironclad.png",
            ];

            $player = new Player($characterData);
            $enemy = $this->enemyPlayer;

            // Set up initial state
            $enemy->setHealth($_SESSION["enemyHealth"]);
            $enemy->setMaxHealth($_SESSION["enemyMaxHealth"]);

            // First, decrement player's status effects
            $player->endTurn();

            // Apply ritual effect to enemy - ONLY ONCE at the beginning of enemy's turn
            if (
                $enemy->hasStatus("ritual") &&
                !isset($_SESSION["ritual_applied_this_turn"])
            ) {
                $enemy->applyRitualEffect();
                $_SESSION["ritual_applied_this_turn"] = true;
            }

            // Execute enemy move
            $enemyMoveMessage = $enemy->executeMove($player);

            $isEnemyDead = $enemy->getHealth() <= 0;

            if (!$isEnemyDead) {
                // Apply enemy's end turn effects after their move
                // (without applying ritual again)
                $enemy->endTurn();
            }

            // Clear ritual flag at end of enemy turn
            unset($_SESSION["ritual_applied_this_turn"]);

            // Discard current hand
            try {
                $player->discardHand();
            } catch (Throwable $discardError) {
                error_log(
                    "Error discarding hand: " . $discardError->getMessage()
                );
                throw $discardError;
            }

            // Handle flex status if present
            if ($player->hasStatus("flex")) {
                $flexAmount = $player->getStatusValue("flex");
                $player->addStatus("strength", -$flexAmount); // Remove the temporary strength
                $player->removeStatus("flex"); // Clear the flex status
            }

            // Reset player energy
            try {
                $player->resetEnergy();
            } catch (Throwable $energyError) {
                error_log(
                    "Error resetting energy: " . $energyError->getMessage()
                );
                throw $energyError;
            }

            // Check if player is dead after enemy's move
            $isPlayerDead = $player->getHealth() <= 0;

            $_SESSION["enemyStatus"] = $enemy->getStatus();

            // Update session state
            $_SESSION["enemyHealth"] = $enemy->getHealth();
            $_SESSION["enemyStatus"] = $enemy->getStatus();
            $_SESSION["enemyBlock"] = $enemy->getBlock();
            $_SESSION["playerHealth"] = $player->getHealth();
            $_SESSION["playerStatus"] = $player->getStatus();
            $_SESSION["playerBlock"] = 0;
            $_SESSION["playerEnergy"] = $player->getEnergy();
            $_SESSION["player_hand"] = null;

            if (isset($_SESSION["enemyStatus"])) {
                $enemy->setStatus($_SESSION["enemyStatus"]);
            }

            // Return updated game state
            $response = [
                "success" => true,
                "enemyHealth" => $enemy->getHealth(),
                "playerHealth" => $player->getHealth(),
                "playerEnergy" => $_SESSION["playerEnergy"],
                "playerBlock" => $_SESSION["playerBlock"],
                "enemyBlock" => $enemy->getBlock(),
                "enemyMoveMessage" => $enemyMoveMessage,
                "nextMove" => $enemy->getNextMove(),
                "moveDescription" => $enemy->getMoveDescription(),
                "isEnemyDead" => $isEnemyDead,
                "playerStatus" => $player->getStatus(),
                "enemyStatus" => $enemy->getStatus(),
                "playerRelics" => $_SESSION["player_relics"] ?? [],
            ];

            if (
                isset($_SESSION["guardian_mode_changed"]) &&
                $_SESSION["guardian_mode_changed"] === true
            ) {
                $response["guardianModeChanged"] = true;
                unset($_SESSION["guardian_mode_changed"]);
            }

            // Check if the defeated enemy was the boss
            $defeatedBoss =
                $isEnemyDead &&
                isset($_SESSION["enemy_type"]) &&
                $_SESSION["enemy_type"] === "boss_guardian";

            if ($isPlayerDead) {
                $response["isPlayerDead"] = true;
                $response["redirect"] = "game/over";
            } elseif ($defeatedBoss) {
                $_SESSION["defeated_boss"] = true;
                $response["redirect"] = "victory";
            } elseif ($isEnemyDead) {
                $response["redirect"] = "reward";
                $_SESSION["nodeCompleted"] = 1;
            }

            // Use the json method from the Controller class to ensure proper JSON response
            $this->json($response);
        } catch (Throwable $e) {
            error_log("Exception in endTurn: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            // Use the json method to ensure valid JSON response even in error cases
            $this->json([
                "success" => false,
                "message" => "Error: " . $e->getMessage(),
            ]);
        }
    }

    public function reward(): void
    {
        try {
            // Apply victory effects
            static $alreadyApplied = false;
            if (!$alreadyApplied) {
                $this->applyVictoryEffects();
                $alreadyApplied = true;
            }

            // Create a temporary player instance just to reset the battle start flag
            $player = new Player([
                "health" => $_SESSION["playerHealth"],
                "max_health" => $_SESSION["playerMaxHealth"],
                "name" => $_SESSION["characterName"],
                "sprite" => "static/images/ironclad.png",
            ]);
            $player->resetBattleStartFlag();

            // Check if the defeated enemy was an elite
            $wasEliteEnemy =
                isset($_SESSION["enemy_type"]) &&
                $_SESSION["enemy_type"] === "elite_nob";

            // Give a relic reward for defeating an elite
            if ($wasEliteEnemy) {
                // Get a random relic appropriate for elite rewards
                $relicId = $this->giveRandomRelic("Common");
                $_SESSION["elite_relic_obtained"] = $relicId; // Track that we gave a relic
            }

            // Clean up GremlinNob-specific session variables if it was a GremlinNob elite
            if (
                isset($_SESSION["enemy_type"]) &&
                $_SESSION["enemy_type"] === "elite_nob"
            ) {
                // Create a GremlinNob instance just to call the cleanup method
                $nob = new GremlinNob();
                $nob->cleanupSession();
            }

            // Clean up Guardian-specific session variables if it was a Guardian boss
            if (
                isset($_SESSION["enemy_type"]) &&
                $_SESSION["enemy_type"] === "boss_guardian"
            ) {
                // Create a Guardian instance just to call the cleanup method
                $guardian = new Guardian();
                $guardian->cleanupSession();
            }

            // Clear enemy data from session since enemy was defeated
            unset($_SESSION["enemy_type"]);
            unset($_SESSION["enemyHealth"]);
            unset($_SESSION["enemyMaxHealth"]);
            unset($_SESSION["enemy_next_move"]);
            unset($_SESSION["enemyStatus"]);
            unset($_SESSION["curl_up_used"]);
            unset($_SESSION["ritual_applied_this_turn"]);
            unset($_SESSION["playerStatus"]);
            unset($_SESSION["battle_start_effects_applied"]);
            unset($_SESSION["guardian_defensive_mode"]);
            unset($_SESSION["guardian_defensive_turns"]);
            unset($_SESSION["guardian_mode_shift_value"]);

            // Properly reset relic states
            if (
                isset($_SESSION["player_relics"]) &&
                is_array($_SESSION["player_relics"])
            ) {
                foreach ($_SESSION["player_relics"] as &$relic) {
                    // For Pen Nib, ensure it doesn't stay permanently active
                    if ($relic["id"] === "pen_nib") {
                        // If it's at 9, explicitly reset to 0
                        if ($relic["counter"] === 9) {
                            $relic["counter"] = 0;
                        }
                    }
                }
            }

            // Pre-battle card cleanup - Remove any temporary cards from discard pile
            // Reconstruct proper discard pile from the permanent deck only
            if (
                isset($_SESSION["player_deck"]) &&
                is_array($_SESSION["player_deck"])
            ) {
                // Reset the discard pile to be empty
                $_SESSION["player_discard_pile"] = [];
            }

            // If this is a POST request, process the selected card
            if ($_SERVER["REQUEST_METHOD"] === "POST") {
                $requestBody = file_get_contents("php://input");

                $data = json_decode($requestBody, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception(
                        "Invalid JSON: " . json_last_error_msg()
                    );
                }

                $selectedCardId = $data["cardId"] ?? null;

                if (!$selectedCardId) {
                    throw new Exception("No card selected");
                }

                // Add the card to the player's deck
                $this->addCardToDeck($selectedCardId);

                // Redirect to map after selection
                $this->json([
                    "success" => true,
                    "redirect" => "map",
                ]);
                return;
            }

            // Otherwise, generate random card rewards
            try {
                $cards = $this->getRandomCardRewards(3);

                // Pass info about elite relic to the view
                $eliteRelicData = null;
                if (isset($_SESSION["elite_relic_obtained"])) {
                    $relicId = $_SESSION["elite_relic_obtained"];

                    // Find the relic data in the session
                    foreach ($_SESSION["player_relics"] as $relic) {
                        if ($relic["id"] === $relicId) {
                            $eliteRelicData = $relic;
                            break;
                        }
                    }
                    unset($_SESSION["elite_relic_obtained"]);
                }

                // Pass the elite relic data to the view
                $this->render("reward", [
                    "cards" => $cards,
                    "eliteRelicData" => $eliteRelicData,
                    "wasEliteEnemy" => $wasEliteEnemy,
                ]);
            } catch (Throwable $e) {
                error_log("Error in reward method: " . $e->getMessage());
                error_log("Stack trace: " . $e->getTraceAsString());

                $this->render("error", [
                    "message" =>
                        "An error occurred while generating rewards: " .
                        $e->getMessage(),
                ]);
            }
        } catch (Throwable $e) {
            error_log("Error in reward method: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            $this->render("error", [
                "message" =>
                    "An error occurred while generating rewards: " .
                    $e->getMessage(),
            ]);
        }
    }

    /**
     * Generate a set of random card rewards
     *
     * @param int $count Number of cards to generate
     * @return array Array of card objects
     */
    private function getRandomCardRewards(int $count): array
    {
        // List of available non-starter cards
        $availableCards = [
            new Anger(),
            new BodySlam(),
            new Clothesline(),
            new Flex(),
            new HeavyBlade(),
            new IronWave(),
            new TwinStrike(),
            new Carnage(),
            new Entrench(),
            new Inflame(),
            new Bludgeon(),
        ];

        // Shuffle the cards
        shuffle($availableCards);

        // Return the first $count cards
        return array_slice($availableCards, 0, $count);
    }

    /**
     * Add a card to the player's deck
     *
     * @param string $cardId ID of the card to add
     */
    private function addCardToDeck(string $cardId): void
    {
        // Map of card IDs to class names
        $cardMap = [
            "anger" => "Anger",
            "body_slam" => "BodySlam",
            "clothesline" => "Clothesline",
            "flex" => "Flex",
            "heavy_blade" => "HeavyBlade",
            "iron_wave" => "IronWave",
            "twin_strike" => "TwinStrike",
            "carnage" => "Carnage",
            "entrench" => "Entrench",
            "inflame" => "Inflame",
            "bludgeon" => "Bludgeon",
            // Add more cards as needed
        ];

        // Check if the card ID exists in our map
        if (!isset($cardMap[$cardId])) {
            throw new Exception("Invalid card ID: " . $cardId);
        }

        // Get the class name
        $className = $cardMap[$cardId];

        // Deserialize the current deck
        $deck = [];
        if (isset($_SESSION["player_deck"])) {
            $deck = $_SESSION["player_deck"];
        }

        // Add the new card to the deck
        $deck[] = [
            "type" => $className,
            "id" => $cardId,
        ];

        // Save the updated deck
        $_SESSION["player_deck"] = $deck;
    }

    private function updateCardPiles(Player $player): void
    {
        $_SESSION["player_hand"] = $this->serializePlayerCards(
            $player->getHand()
        );
        $_SESSION["player_discard_pile"] = $this->serializePlayerCards(
            $player->getDiscardPile()
        );
        $_SESSION["player_draw_pile"] = $this->serializePlayerCards(
            $player->getDrawPile()
        );
    }

    private function serializePlayerCards(array $cards): array
    {
        return array_map(function ($card) {
            return [
                "type" => get_class($card),
                "id" => $card->getId(),
            ];
        }, $cards);
    }

    public function gameOver(): void
    {
        // Ensure we have a valid player
        if (!isset($_SESSION["username"])) {
            $this->redirect("login");
            return;
        }

        // Render the game over screen
        $this->render("game-over");
    }

    public function restart(): void
    {
        // Clear all game-related session data
        unset($_SESSION["playerHealth"]);
        unset($_SESSION["playerMaxHealth"]);
        unset($_SESSION["playerBlock"]);
        unset($_SESSION["playerEnergy"]);
        unset($_SESSION["playerMaxEnergy"]);
        unset($_SESSION["playerStatus"]);
        unset($_SESSION["enemy_type"]);
        unset($_SESSION["enemyHealth"]);
        unset($_SESSION["enemyMaxHealth"]);
        unset($_SESSION["enemy_next_move"]);
        unset($_SESSION["enemyStatus"]);
        unset($_SESSION["floor"]);
        unset($_SESSION["money"]);
        unset($_SESSION["player_hand"]);
        unset($_SESSION["player_deck"]);
        unset($_SESSION["player_draw_pile"]);
        unset($_SESSION["player_discard_pile"]);

        // Redirect to start a new game
        $this->redirect("map");
    }

    public function obtainRelic(string $relicId, bool $showScreen = true): void
    {
        try {
            // Create the relic based on ID
            $relic = null;
            switch ($relicId) {
                case "burning_blood":
                    $relic = new BurningBlood();
                    break;
                case "akabeko":
                    $relic = new Akabeko();
                    break;
                case "oddly_smooth_stone":
                    $relic = new OddlySmoothStone();
                    break;
                case "pen_nib":
                    $relic = new PenNib();
                    break;
                case "vajra":
                    $relic = new Vajra();
                    break;
                default:
                    throw new Exception("Unknown relic ID: " . $relicId);
            }

            if (!$relic) {
                throw new Exception(
                    "Failed to create relic with ID: " . $relicId
                );
            }

            // Store the relic in session
            if (!isset($_SESSION["player_relics"])) {
                $_SESSION["player_relics"] = [];
            }

            // Check if player already has this relic
            foreach ($_SESSION["player_relics"] as $existingRelic) {
                if ($existingRelic["id"] === $relicId) {
                    return;
                }
            }

            // Add to session
            $_SESSION["player_relics"][] = [
                "id" => $relic->getId(),
                "name" => $relic->getName(),
                "description" => $relic->getDescription(),
                "image" => $relic->getImage(),
                "rarity" => $relic->getRarity(),
                "counter" => $relic->getCounter(),
            ];

            // We don't need to apply effects here - they'll be applied at battle start

            // Only show the relic obtained screen if showScreen is true
            if ($showScreen) {
                $this->render("relic-obtained", ["relic" => $relic]);
            }
        } catch (Exception $e) {
            error_log("Error in obtainRelic: " . $e->getMessage());
            if ($showScreen) {
                $this->render("error", [
                    "message" =>
                        "An error occurred while obtaining a relic: " .
                        $e->getMessage(),
                ]);
            }
        }
    }

    public function giveRandomRelic(string $rarity = "Common"): string
    {
        $commonRelics = ["akabeko", "pen_nib", "vajra", "oddly_smooth_stone"];
        $uncommonRelics = []; // Add uncommon relics here
        $rareRelics = []; // Add rare relics here

        switch ($rarity) {
            case "Common":
                $pool = $commonRelics;
                break;
            case "Uncommon":
                $pool = $uncommonRelics;
                break;
            case "Rare":
                $pool = $rareRelics;
                break;
            default:
                $pool = $commonRelics;
        }

        // Select a random relic from the pool
        $relicId = $pool[array_rand($pool)];
        $this->obtainRelic($relicId);

        return $relicId;
    }

    private function applyVictoryEffects(): void
    {
        try {
            // Only proceed if the player has some relics
            if (
                !isset($_SESSION["player_relics"]) ||
                empty($_SESSION["player_relics"])
            ) {
                return;
            }

            //gain gold
            $_SESSION["money"] += rand(10, 20);

            // Check for Burning Blood
            foreach ($_SESSION["player_relics"] as $relicData) {
                if ($relicData["id"] === "burning_blood") {
                    // Store old health for logging
                    $oldHealth = $_SESSION["playerHealth"];

                    // Apply the Burning Blood healing directly
                    $_SESSION["playerHealth"] = min(
                        $_SESSION["playerMaxHealth"],
                        $_SESSION["playerHealth"] + 6
                    );

                    // Log healing amount

                    // Found and applied Burning Blood, no need to check other relics
                    break;
                }
            }
        } catch (Exception $e) {
            error_log("Error applying victory effects: " . $e->getMessage());
        }
    }

    public function bossEncounter(): void
    {
        // Check if we're already in a boss fight
        $alreadyInBossFight =
            isset($_SESSION["enemy_type"]) &&
            $_SESSION["enemy_type"] === "boss_guardian" &&
            isset($_SESSION["enemyHealth"]);

        if (!$alreadyInBossFight) {
            // Clear all previous enemy data to start fresh
            unset($_SESSION["enemyHealth"]);
            unset($_SESSION["enemyMaxHealth"]);
            unset($_SESSION["enemy_next_move"]);
            unset($_SESSION["enemyStatus"]);
            unset($_SESSION["guardian_mode_shift_value"]);
            unset($_SESSION["guardian_defensive_mode"]);
            unset($_SESSION["guardian_defensive_turns"]);

            // Set the enemy type first so constructor uses correct logic
            $_SESSION["enemy_type"] = "boss_guardian";

            // Create Guardian instance (will now use the boss sprite due to $_SESSION["enemy_type"] being set)
            $guardian = new Guardian();

            // Store its initial state
            $_SESSION["enemyHealth"] = $guardian->getHealth();
            $_SESSION["enemyMaxHealth"] = $guardian->getMaxHealth();
            $_SESSION["enemyStatus"] = $guardian->getStatus();

            // Store a flag to indicate this is a new boss encounter
            $_SESSION["new_boss_encounter"] = true;
        }

        // Render the battle
        $this->index();
    }

    public function eliteEncounter(): void
    {
        // Check if we're already in an elite fight
        $alreadyInEliteFight =
            isset($_SESSION["enemy_type"]) &&
            $_SESSION["enemy_type"] === "elite_nob" &&
            isset($_SESSION["enemyHealth"]);

        if (!$alreadyInEliteFight) {
            // Clear all previous enemy data to start fresh
            unset($_SESSION["enemyHealth"]);
            unset($_SESSION["enemyMaxHealth"]);
            unset($_SESSION["enemy_next_move"]);
            unset($_SESSION["enemyStatus"]);
            unset($_SESSION["nob_has_used_bellow"]);

            // Set the enemy type first so constructor uses correct logic
            $_SESSION["enemy_type"] = "elite_nob";

            // Create GremlinNob instance
            $nob = new GremlinNob();

            // Store its initial state
            $_SESSION["enemyHealth"] = $nob->getHealth();
            $_SESSION["enemyMaxHealth"] = $nob->getMaxHealth();
            $_SESSION["enemyStatus"] = $nob->getStatus();

            // Store a flag to indicate this is a new elite encounter
            $_SESSION["new_elite_encounter"] = true;
        }

        // Render the battle
        $this->index();
    }

    public function victory(): void
    {
        // Check if player is logged in
        if (!isset($_SESSION["username"])) {
            $this->redirect("login");
            return;
        }

        // Ensure we came from a boss fight
        $fromBossFight =
            isset($_SESSION["defeated_boss"]) &&
            $_SESSION["defeated_boss"] === true;

        if (!$fromBossFight) {
            // If trying to access victory screen directly, redirect to map
            $this->redirect("map");
            return;
        }

        // Calculate a score based on various factors
        $score = $this->calculateVictoryScore();
        $_SESSION["score"] = $score;

        // Make sure we have a start time recorded
        if (!isset($_SESSION["start_time"])) {
            $_SESSION["start_time"] = time() - 1200; // Default to 20 minutes ago if missing
        }

        // Render the victory screen
        $this->render("victory");
    }

    private function calculateVictoryScore(): int
    {
        $score = 0;

        // Base score for completing the game
        $score += 100;

        // Bonus for health remaining (percentage of max health)
        $healthPercentage =
            $_SESSION["playerHealth"] / $_SESSION["playerMaxHealth"];
        $score += round($healthPercentage * 50);

        // Bonus for gold
        $score += min(50, $_SESSION["money"] / 2);

        // Bonus for relics collected
        if (
            isset($_SESSION["player_relics"]) &&
            is_array($_SESSION["player_relics"])
        ) {
            $score += count($_SESSION["player_relics"]) * 10;
        }

        return $score;
    }
}
