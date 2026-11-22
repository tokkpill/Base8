<?php

declare(strict_types=1);

namespace Model;

/**
 * Clase AssetReview
 * 
 * Gestiona las reseñas y calificaciones de recursos académicos.
 * Solo compradores verificados pueden dejar reseñas.
 */
class AssetReview extends ActiveRecord
{
    protected static string $tabla = 'asset_reviews';
    protected static array $columnasDB = ['id', 'asset_id', 'usuario_id', 'transaction_id', 'rating', 'comentario'];

    public ?int $id;
    public int $asset_id;
    public int $usuario_id;
    public ?int $transaction_id;
    public int $rating;
    public ?string $comentario;

    /** @var Usuario|null Autor de la reseña */
    public ?Usuario $usuario = null;

    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->asset_id = (int)($args['asset_id'] ?? 0);
        $this->usuario_id = (int)($args['usuario_id'] ?? 0);
        $this->transaction_id = isset($args['transaction_id']) ? (int)$args['transaction_id'] : null;
        $this->rating = (int)($args['rating'] ?? 5);
        $this->comentario = $args['comentario'] ?? null;
    }

    /**
     * Valida la reseña
     */
    public function validar(): array
    {
        if ($this->rating < 1 || $this->rating > 5) {
            self::$alertas['error'][] = 'La calificación debe ser entre 1 y 5';
        }
        if (!$this->asset_id) {
            self::$alertas['error'][] = 'El recurso es obligatorio';
        }
        return self::$alertas;
    }

    /**
     * Obtiene reseñas de un asset con datos del usuario
     */
    public static function getByAsset(int $assetId, int $limit = 20): array
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE asset_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("ii", $assetId, $limit);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $review = static::crearObjeto($registro);
            $review->usuario = Usuario::find($review->usuario_id);
            $array[] = $review;
        }
        $stmt->close();
        return $array;
    }

    /**
     * Verifica si un usuario ya dejó una reseña para un asset
     */
    public static function hasReviewed(int $userId, int $assetId): bool
    {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla . " WHERE usuario_id = ? AND asset_id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("ii", $userId, $assetId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)($result['total'] ?? 0) > 0;
    }
}
