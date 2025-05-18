<?php
require_once __DIR__ . "/../core/Controller.php";

class RestController extends Controller
{
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

        // If POST request, apply healing and continue
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $this->applyRest();

            // Set nodeCompleted to 1 to indicate this node has been completed
            // This is the key change to make rest sites update map progression
            $_SESSION["nodeCompleted"] = 1;

            $this->redirect("map");
            return;
        }

        // Calculate healing amount (30% of max HP)
        $maxHP = $_SESSION["playerMaxHealth"];
        $healAmount = ceil($maxHP * 0.3);
        $currentHP = $_SESSION["playerHealth"];

        // If player is at full health, still show the rest site but note that
        $isFullHealth = $currentHP >= $maxHP;

        $this->render("rest-site", [
            "healAmount" => $healAmount,
            "currentHP" => $currentHP,
            "maxHP" => $maxHP,
            "isFullHealth" => $isFullHealth,
        ]);
    }

    private function applyRest(): void
    {
        // Calculate healing (30% of max HP)
        $maxHP = $_SESSION["playerMaxHealth"];
        $healAmount = ceil($maxHP * 0.3);

        // Apply healing, ensuring we don't exceed max HP
        $newHealth = min($_SESSION["playerHealth"] + $healAmount, $maxHP);
        $_SESSION["playerHealth"] = $newHealth;

        // Reset any temporary status effects that should clear after resting
        if (isset($_SESSION["playerStatus"])) {
            // Remove temporary statuses
            $persistentStatuses = [];
            foreach ($_SESSION["playerStatus"] as $status => $value) {
                // Add statuses that should persist across rest sites
                $persistentStatuses[$status] = $value;
            }
            $_SESSION["playerStatus"] = $persistentStatuses;
        }
    }
}
