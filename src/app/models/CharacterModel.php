<?php
class CharacterModel extends BaseORM
{
    protected $table = "characters"; // Changed from "character" to "characters"
    protected $primaryKey = "character_id";

    public function getCharacterDetails(int $characterId): array|bool
    {
        $character = $this->findById($characterId);
        if ($character) {
            return [
                "health" => $character["base_hp"],
                "max_health" => $character["base_hp"],
                "name" => $character["name"],
                "sprite" => "static/images/ironclad.png",
            ];
        }
        return false;

    }

    /**
     * phpactor, SHUTUP!
     * @param array $characterData
     */
    public function createPlayer(array $characterData): Player
    {
        return new Player($characterData);
    }
}
