<?php

require_once "Relic.php";

class Akabeko extends Relic
{
    public function __construct()
    {
        parent::__construct(
            "akabeko",
            "Akabeko",
            "Your first attack each combat deals 8 additional damage.",
            "static/images/temp/images/largeRelics/akabeko.png",
            "Common"
        );
        $this->counter = 1; // 1 means ready to use
    }

    public function onBattleStart($player): void
    {
        $this->counter = 1; // Reset at the beginning of each battle
        $this->updateCounterInSession();
    }

    public function onDamageDealt($player, int $damage): int
    {
        if ($this->counter === 1) {
            $this->counter = 0; // Used up for this combat
            $this->updateCounterInSession();
            return $damage + 8;
        }
        return $damage;
    }

    private function updateCounterInSession(): void
    {
        if (isset($_SESSION["player_relics"])) {
            foreach ($_SESSION["player_relics"] as &$relic) {
                if ($relic["id"] === "akabeko") {
                    $relic["counter"] = $this->counter;
                    break;
                }
            }
        }
    }
}
