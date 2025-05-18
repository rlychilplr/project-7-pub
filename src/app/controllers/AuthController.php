<?php
require_once __DIR__ . "/../core/Controller.php";
require_once __DIR__ . "/../models/AuthModel.php";

class AuthController extends Controller
{
    private $authModel;

    public function __construct()
    {
        $this->authModel = new AuthModel();
    }

    public function login(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $username = $_POST["username"] ?? "";
            $password = $_POST["password"] ?? "";

            $user = $this->authModel->login($username, $password);

            if ($user) {
                $_SESSION["user_id"] = $user["player_id"];
                $_SESSION["username"] = $user["username"];
                $this->redirect("");
                return;
            }

            $error = "Invalid username or password";
            $this->render("login", ["error" => $error]);
            return;
        }

        $this->render("login");
    }

    public function register(): void
    {
        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            $result = $this->authModel->register($_POST);

            if (isset($result["player_id"]) && isset($result["username"])) {
                // Registration successful
                $_SESSION["user_id"] = $result["player_id"];
                $_SESSION["username"] = $result["username"];
                $this->redirect("");
                return;
            }

            $this->render("register", [
                "errors" => $result,
                "username" => $_POST["username"] ?? "",
                "email" => $_POST["email"] ?? "",
            ]);
            return;
        }

        $this->render("register");
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect("login");
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION["user_id"]);
    }

    public function getCurrentUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return $this->authModel->getCurrentUser($_SESSION["user_id"]);
    }
}
