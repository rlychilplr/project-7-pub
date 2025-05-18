<?php
require_once __DIR__ . "/Card.php";

class Flex extends Card
{
    public function __construct()
    {
        parent::__construct(
            "flex",
            "Flex",
            "Gain 2 Strength. At the end of your turn, lose 2 Strength. ",
            0,
            "Skill",
            "Common",
            "static/images/temp/images/1024Portraits/red/skill/flex.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        $player->addStatus("strength", 2);
        $player->addStatus("flex", 2);
    }
}
