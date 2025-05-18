<?php
require_once __DIR__ . "/Card.php";

class Entrench extends Card
{
    public function __construct()
    {
        parent::__construct(
            "entrench",
            "Entrench",
            "Double your current Block.",
            2,
            "Skill",
            "Uncommon",
            "static/images/temp/images/1024Portraits/red/skill/entrench.png"
        );
    }

    public function play(Player $player, Enemy $target): void
    {
        $currentBlock = $player->getBlock();
        $player->addBlock($currentBlock, false);
    }
}
