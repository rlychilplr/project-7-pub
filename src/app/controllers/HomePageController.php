<?php
require_once __DIR__ . "/../core/Controller.php";

class HomePageController extends Controller
{
    private $authController;

    public function __construct()
    {
        $this->authController = new AuthController();
    }

    public function index(): void
    {
        $isLoggedIn = $this->authController->isLoggedIn();
        $user = null;

        if ($isLoggedIn) {
            $user = $this->authController->getCurrentUser();

            // If user data couldn't be retrieved, reset login status
            if (!$user) {
                $isLoggedIn = false;
                // Redirect to login to clear any invalid session
                $this->redirect("login");
                return;
            }

            // Check if player died and clear game state
            if (
                isset($_SESSION["playerHealth"]) &&
                $_SESSION["playerHealth"] <= 0
            ) {
                // Clear game state but maintain login
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
            }
        }

        $data = [
            "user" => $user,
            "isLoggedIn" => $isLoggedIn,
        ];

        $this->render("home", $data);
    }
}
