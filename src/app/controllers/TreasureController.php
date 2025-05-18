<?php
require_once __DIR__ . "/../core/Controller.php";

class TreasureController extends Controller
{
    private $gameController;

    public function __construct()
    {
        $this->gameController = new GameController();
    }

    public function index(): void
    {
        // Check if player is logged in
        if (!isset($_SESSION["username"])) {
            $this->redirect("login");
            return;
        }

        // Store the node ID from the URL if present
        if (isset($_GET["node_id"])) {
            $_SESSION["nodeid"] = $_GET["node_id"];
        }

        // If POST request, user has clicked to collect the relic
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            // Mark this node as completed so the map will show next nodes
            $_SESSION["nodeCompleted"] = 1;

            $this->redirect("map");
            return;
        }

        // Generate a random relic and render the treasure room
        $relicId = $this->giveUniqueRandomRelic();

        $this->render("treasure-room", [
            "message" => "You've found a treasure chest!",
            "relicId" => $relicId,
        ]);
    }

    /**
     * Give the player a random relic they don't already have
     *
     * @return string|null The ID of the relic that was given, or null if all relics are owned
     */
    private function giveUniqueRandomRelic(): ?string
    {
        // List of available relics (excluding Burning Blood which is the starter relic)
        $availableRelics = [
            "akabeko",
            "oddly_smooth_stone",
            "pen_nib",
            "vajra",
        ];

        // Get the player's current relics
        $playerRelics = [];
        if (
            isset($_SESSION["player_relics"]) &&
            is_array($_SESSION["player_relics"])
        ) {
            foreach ($_SESSION["player_relics"] as $relic) {
                $playerRelics[] = $relic["id"];
            }
        }

        // Filter out relics the player already has
        $availableRelics = array_filter($availableRelics, function (
            $relicId
        ) use ($playerRelics) {
            return !in_array($relicId, $playerRelics);
        });

        // If there are no more unique relics to give, return null
        if (empty($availableRelics)) {
            // Show a message and give gold instead
            $_SESSION["money"] += 25;
            $this->render("treasure-room", [
                "message" =>
                    "You already own all available relics. You found 25 gold instead!",
                "goldOnly" => true,
            ]);
            return null;
        }

        // Select a random relic from the available ones
        $selectedRelicId = $availableRelics[array_rand($availableRelics)];

        // Let the GameController handle giving the relic
        $this->gameController->obtainRelic($selectedRelicId);

        return $selectedRelicId;
    }
}
