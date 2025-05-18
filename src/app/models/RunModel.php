<?php 
class RunModel extends BaseORM {
    protected $table = 'run';
    protected $primaryKey = 'run_id';

    public function startNewRun(int $playerId, int $characterId, int $ascensionLevel = 0): int {
        return $this->save([
            'player_id' => $playerId,
            'character_id' => $characterId,
            'start_time' => date('Y-m-d H:i:s'),
            'ascension_level' => $ascensionLevel
        ]);
    }

    public function endRun(int $runId, bool $victory, int $floorReached, int $score): bool {
        return $this->save([
            'end_time' => date('Y-m-d H:i:s'),
            'victory' => $victory,
            'floor_reached' => $floorReached,
            'score' => $score
        ], $runId);
    }

    public function getPlayerStats(int $playerId): array {
        $sql = "SELECT
                COUNT(*) as total_runs,
                SUM(CASE WHEN victory = 1 THEN 1 ELSE 0 END) as victories,
                MAX(floor_reached) as highest_floor,
                MAX(score) as highest_score
                FROM run
                WHERE player_id = ?";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$playerId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
