<?php
require_once __DIR__ . "/../core/Controller.php";
require_once __DIR__ . "/../models/Entity/Player.php";

class MapController extends Controller
{
    public function __construct()
    {
        if (!isset($_SESSION["level"])) {
            $_SESSION["level"] = 1;
        }

        if (!isset($_SESSION["nodeCompleted"])) {
            $_SESSION["nodeCompleted"] = 0;
        }
        if (!isset($_SESSION["nodeid"])) {
            $_SESSION["nodeid"] = 0;
        }

        // Initialize playerMaxEnergy if it's not set yet
        if (!isset($_SESSION["playerMaxEnergy"])) {
            $_SESSION["playerMaxEnergy"] = 3;
        }

        // Initialize playerEnergy if it's not set yet
        if (!isset($_SESSION["playerEnergy"])) {
            $_SESSION["playerEnergy"] = $_SESSION["playerMaxEnergy"];
        }
    }

    public function index(): void
    {
        // Reset battle-specific session variables while maintaining deck and progress
        $_SESSION["playerBlock"] = 0;
        $_SESSION["playerEnergy"] = $_SESSION["playerMaxEnergy"];
        $_SESSION["player_hand"] = null; // Force a fresh hand to be drawn in next battle

        // Important: Don't lose cards between battles - check and log card counts
        $drawPileCount = isset($_SESSION["player_draw_pile"])
            ? count($_SESSION["player_draw_pile"])
            : 0;
        $discardPileCount = isset($_SESSION["player_discard_pile"])
            ? count($_SESSION["player_discard_pile"])
            : 0;

        $this->render("map");
    }
}
