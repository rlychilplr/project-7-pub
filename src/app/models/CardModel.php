<?php

class CardModel extends BaseORM
{
    protected $table = "cards";
    protected $primaryKey = "card_id";

    public function getStarterDeckForCharacter(int $characterId): array
    {
        return $this->findBy([
            "character_id" => $characterId,
            "is_starter" => true,
        ]);
    }
}
