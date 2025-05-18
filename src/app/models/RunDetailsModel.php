<?php

class RunDetailsModel extends BaseORM
{
    protected $table = "run_details";
    protected $primaryKey = "detail_id";

    public function addCardToRun(
        int $runId,
        int $cardId,
        int $quantity = 1
    ): bool {
        return $this->save([
            "run_id" => $runId,
            "detail_type" => "card",
            "card_id" => $cardId,
            "quantity" => $quantity,
        ]);
    }

    public function addRelicToRun(
        int $runId,
        int $relicId,
        int $floorObtained
    ): bool {
        return $this->save([
            "run_id" => $runId,
            "detail_type" => "relic",
            "relic_id" => $relicId,
            "floor" => $floorObtained,
        ]);
    }

    public function addEncounter(
        int $runId,
        int $floor,
        string $encounterType,
        string $result
    ): int {
        return $this->save([
            "run_id" => $runId,
            "detail_type" => "encounter",
            "floor" => $floor,
            "encounter_type" => $encounterType,
            "result" => $result,
        ]);
    }

    public function getRunDeck(int $runId): array
    {
        // Get cards in deck
        $sql = "SELECT c.*, rd.quantity
                FROM cards c
                JOIN run_details rd ON c.card_id = rd.card_id
                WHERE rd.run_id = ? AND rd.detail_type = 'card'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$runId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRunRelics(int $runId): array
    {
        $sql = "SELECT r.*, rd.floor as floor_obtained
                FROM relics r
                JOIN run_details rd ON r.relic_id = rd.relic_id
                WHERE rd.run_id = ? AND rd.detail_type = 'relic'";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$runId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRunEncounters(int $runId): array
    {
        return $this->findBy(
            [
                "run_id" => $runId,
                "detail_type" => "encounter",
            ],
            ["floor" => "ASC"]
        );
    }
}
