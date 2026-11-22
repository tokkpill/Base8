<?php

declare(strict_types=1);

namespace Model;

/**
 * Clase ReputationLog
 * 
 * Registra eventos de prestigio y calcula la reputación de un usuario.
 * 
 * Fórmula: Prestige = (Downloads × 0.4) + (AvgRating × 0.6) + (VerifiedContributions × 1.5)
 */
class ReputationLog extends ActiveRecord
{
    protected static string $tabla = 'reputation_log';
    protected static array $columnasDB = ['id', 'usuario_id', 'tipo', 'puntos', 'referencia_id', 'descripcion'];

    public ?int $id;
    public int $usuario_id;
    public string $tipo;
    public float $puntos;
    public ?int $referencia_id;
    public ?string $descripcion;

    /** Constantes de peso para el algoritmo */
    private const WEIGHT_DOWNLOADS = 0.4;
    private const WEIGHT_RATING = 0.6;
    private const WEIGHT_CONTRIBUTIONS = 1.5;

    /** Umbrales de insignias */
    private const BADGE_THRESHOLDS = [
        'Elite Scholar'    => 500,
        'Top Contributor'  => 200,
        'Rising Star'      => 100,
        'Scholar'          => 50,
        'Contributor'      => 20,
        'Novato'           => 0,
    ];

    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = (int)($args['usuario_id'] ?? 0);
        $this->tipo = $args['tipo'] ?? 'contribucion';
        $this->puntos = (float)($args['puntos'] ?? 0.00);
        $this->referencia_id = isset($args['referencia_id']) ? (int)$args['referencia_id'] : null;
        $this->descripcion = $args['descripcion'] ?? null;
    }

    /**
     * Calcula el prestigio total de un usuario usando la fórmula oficial
     * 
     * @param int $userId ID del usuario
     * @return float Puntos de prestigio totales
     */
    public static function calculateUserPrestige(int $userId): float
    {
        // Total de descargas del usuario (como autor)
        $queryDownloads = "SELECT COALESCE(SUM(descargas), 0) as total FROM academic_assets WHERE autor_id = ? AND estado = 'publicado'";
        $stmt = self::$db->prepare($queryDownloads);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $totalDownloads = (float)($result['total'] ?? 0);
        $stmt->close();

        // Rating promedio del usuario (promedio de sus assets)
        $queryRating = "SELECT COALESCE(AVG(rating_promedio), 0) as avg_rating FROM academic_assets WHERE autor_id = ? AND estado = 'publicado' AND total_ratings > 0";
        $stmt = self::$db->prepare($queryRating);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $avgRating = (float)($result['avg_rating'] ?? 0);
        $stmt->close();

        // Contribuciones verificadas (assets publicados con al menos 1 descarga)
        $queryContributions = "SELECT COUNT(*) as total FROM academic_assets WHERE autor_id = ? AND estado = 'publicado' AND descargas > 0";
        $stmt = self::$db->prepare($queryContributions);
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $verifiedContributions = (float)($result['total'] ?? 0);
        $stmt->close();

        // Aplicar la fórmula
        $prestige = ($totalDownloads * self::WEIGHT_DOWNLOADS)
                  + ($avgRating * self::WEIGHT_RATING)
                  + ($verifiedContributions * self::WEIGHT_CONTRIBUTIONS);

        return round($prestige, 2);
    }

    /**
     * Determina la insignia de reputación basada en los puntos
     * 
     * @param float $points Puntos de prestigio
     * @return string Nombre de la insignia
     */
    public static function getBadgeForPoints(float $points): string
    {
        foreach (self::BADGE_THRESHOLDS as $badge => $threshold) {
            if ($points >= $threshold) {
                return $badge;
            }
        }
        return 'Novato';
    }

    /**
     * Recalcula y actualiza los puntos de prestigio y la insignia de un usuario
     */
    public static function updateUserPrestige(int $userId): void
    {
        $points = self::calculateUserPrestige($userId);
        $badge = self::getBadgeForPoints($points);

        $query = "UPDATE usuarios SET prestige_points = ?, reputation_badge = ? WHERE id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("dsi", $points, $badge, $userId);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Registra un evento de reputación
     */
    public static function logEvent(int $userId, string $tipo, float $puntos, ?int $referenciaId = null, ?string $descripcion = null): void
    {
        $log = new self([
            'usuario_id' => $userId,
            'tipo' => $tipo,
            'puntos' => $puntos,
            'referencia_id' => $referenciaId,
            'descripcion' => $descripcion,
        ]);
        $log->crear();

        // Recalcular prestigio del usuario
        self::updateUserPrestige($userId);
    }

    /**
     * Obtiene el historial de reputación de un usuario
     */
    public static function getUserHistory(int $userId, int $limit = 50): array
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE usuario_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("ii", $userId, $limit);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }
        $stmt->close();
        return $array;
    }

    /**
     * Devuelve los umbrales de insignias (para mostrar en UI)
     */
    public static function getBadgeThresholds(): array
    {
        return self::BADGE_THRESHOLDS;
    }
}
