<?php
require_once __DIR__ . "/BaseORM.php";
require_once __DIR__ . "/UserModel.php";

class AuthModel extends BaseORM
{
    private $userModel;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new UserModel();
    }

    public function login(string $username, string $password): ?array
    {
        $user = $this->userModel->findByUsername($username);

        if ($user && password_verify($password, $user["password_hash"])) {
            // Update last login
            $this->userModel->updateLastLogin($user["player_id"]);
            return $user;
        }

        return null;
    }

    /**
     * shut up phpactor
     * @param array $data
     */
    public function register(array $data): array|bool
    {
        $errors = [];

        // Validate username
        if (empty($data["username"])) {
            $errors["username"] = "Username is required";
        } elseif ($this->userModel->findByUsername($data["username"])) {
            $errors["username"] = "Username already exists";
        }

        // Validate email
        if (empty($data["email"])) {
            $errors["email"] = "Email is required";
        } elseif (!filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
            $errors["email"] = "Invalid email format";
        } elseif ($this->userModel->findByEmail($data["email"])) {
            $errors["email"] = "Email already exists";
        }

        // Validate password
        if (empty($data["password"])) {
            $errors["password"] = "Password is required";
        } elseif (strlen($data["password"]) < 8) {
            $errors["password"] = "Password must be at least 8 characters";
        }

        // Validate password confirmation
        if ($data["password"] !== $data["confirm_password"]) {
            $errors["confirm_password"] = "Passwords do not match";
        }

        if (!empty($errors)) {
            return $errors;
        }

        // Create user
        $userId = $this->userModel->save([
            "username" => $data["username"],
            "email" => $data["email"],
            "password_hash" => password_hash(
                $data["password"],
                PASSWORD_DEFAULT
            ),
        ]);

        if (!$userId) {
            return ["general" => "Registration failed"];
        }

        return $this->userModel->findById($userId);
    }

    public function getCurrentUser(int $userId): ?array
    {
        return $this->userModel->findById($userId);
    }
}
