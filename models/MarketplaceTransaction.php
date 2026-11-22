<?php

declare(strict_types=1);

namespace Model;

/**
 * Clase MarketplaceTransaction
 * 
 * Gestiona el flujo de transacciones P2P del marketplace.
 * Estados: pendiente → completada → verificada (o disputada/reembolsada)
 */
class MarketplaceTransaction extends ActiveRecord
{
    protected static string $tabla = 'marketplace_transactions';
    protected static array $columnasDB = [
        'id', 'asset_id', 'comprador_id', 'vendedor_id', 'monto',
        'estado', 'metodo_pago', 'referencia_pago', 'notas'
    ];

    public ?int $id;
    public int $asset_id;
    public int $comprador_id;
    public int $vendedor_id;
    public float $monto;
    public string $estado;
    public string $metodo_pago;
    public ?string $referencia_pago;
    public ?string $notas;

    /** @var AcademicAsset|null Recurso de la transacción */
    public ?AcademicAsset $asset = null;
    /** @var Usuario|null Comprador */
    public ?Usuario $comprador = null;
    /** @var Usuario|null Vendedor */
    public ?Usuario $vendedor = null;

    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->asset_id = (int)($args['asset_id'] ?? 0);
        $this->comprador_id = (int)($args['comprador_id'] ?? 0);
        $this->vendedor_id = (int)($args['vendedor_id'] ?? 0);
        $this->monto = (float)($args['monto'] ?? 0.00);
        $this->estado = $args['estado'] ?? 'pendiente';
        $this->metodo_pago = $args['metodo_pago'] ?? 'creditos';
        $this->referencia_pago = $args['referencia_pago'] ?? null;
        $this->notas = $args['notas'] ?? null;
    }

    /**
     * Inicia una compra P2P
     */
    public static function initiatePurchase(int $assetId, int $compradorId): array
    {
        $asset = AcademicAsset::find($assetId);
        if (!$asset) {
            return ['resultado' => false, 'error' => 'El recurso no existe'];
        }

        if ($asset->autor_id === $compradorId) {
            return ['resultado' => false, 'error' => 'No puedes comprar tu propio recurso'];
        }

        // Verificar si ya compró este asset
        $existing = self::whereArray([
            'asset_id' => $assetId,
            'comprador_id' => $compradorId,
            'estado' => 'completada'
        ]);
        if (!empty($existing)) {
            return ['resultado' => false, 'error' => 'Ya adquiriste este recurso'];
        }

        $tx = new self([
            'asset_id' => $assetId,
            'comprador_id' => $compradorId,
            'vendedor_id' => $asset->autor_id,
            'monto' => $asset->precio,
        ]);

        $resultado = $tx->crear();

        if ($resultado['resultado']) {
            // Auto-completar si es gratis
            if ($asset->precio <= 0) {
                $tx->id = $resultado['id'];
                $tx->completePurchase();
            }
        }

        return $resultado;
    }

    /**
     * Completa la transacción
     */
    public function completePurchase(): bool
    {
        $this->estado = 'completada';
        $result = $this->actualizar();

        if ($result) {
            // Incrementar descargas del asset
            $asset = AcademicAsset::find($this->asset_id);
            if ($asset) {
                $asset->incrementDownloads();
            }

            // Registrar evento de reputación para el vendedor
            ReputationLog::logEvent(
                $this->vendedor_id,
                'venta',
                $this->monto > 0 ? 2.0 : 0.5,
                $this->asset_id,
                'Venta de recurso completada'
            );
        }

        return $result;
    }

    /**
     * Obtiene transacciones de un comprador
     */
    public static function getByComprador(int $compradorId, int $limit = 50): array
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE comprador_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("ii", $compradorId, $limit);
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
     * Obtiene transacciones de un vendedor
     */
    public static function getByVendedor(int $vendedorId, int $limit = 50): array
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE vendedor_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("ii", $vendedorId, $limit);
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
     * Verifica si un usuario ya compró un asset
     */
    public static function hasPurchased(int $userId, int $assetId): bool
    {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla . " WHERE comprador_id = ? AND asset_id = ? AND estado IN ('completada', 'verificada')";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("ii", $userId, $assetId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (int)($result['total'] ?? 0) > 0;
    }

    /**
     * Calcula ingresos totales de un vendedor
     */
    public static function totalRevenue(int $vendedorId): float
    {
        $query = "SELECT COALESCE(SUM(monto), 0) as total FROM " . static::$tabla . " WHERE vendedor_id = ? AND estado IN ('completada', 'verificada')";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $vendedorId);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        return (float)($result['total'] ?? 0);
    }
}
