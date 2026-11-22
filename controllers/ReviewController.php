<?php

declare(strict_types=1);

namespace Controllers;

use Model\AssetReview;
use Model\AcademicAsset;
use Model\MarketplaceTransaction;
use Model\ReputationLog;
use MVC\Router;

/**
 * Clase ReviewController
 * 
 * Gestiona las reseñas de recursos académicos.
 * Solo compradores verificados pueden crear reseñas.
 */
class ReviewController
{
    /**
     * Crear una nueva reseña (POST)
     */
    public static function create(Router $router): void
    {
        if (!is_auth()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /marketplace');
            exit;
        }

        if (!verify_csrf()) {
            header('Location: /marketplace');
            exit;
        }

        $assetId = (int)($_POST['asset_id'] ?? 0);
        $userId = (int)$_SESSION['id'];
        $rating = (int)($_POST['rating'] ?? 5);
        $comentario = s($_POST['comentario'] ?? '');

        // Verificar que el usuario compró el asset
        if (!MarketplaceTransaction::hasPurchased($userId, $assetId)) {
            header('Location: /marketplace/asset?id=' . $assetId . '&error=no-purchase');
            exit;
        }

        // Verificar que no haya dejado reseña antes
        if (AssetReview::hasReviewed($userId, $assetId)) {
            header('Location: /marketplace/asset?id=' . $assetId . '&error=already-reviewed');
            exit;
        }

        $review = new AssetReview([
            'asset_id' => $assetId,
            'usuario_id' => $userId,
            'rating' => $rating,
            'comentario' => $comentario,
        ]);

        $alertas = $review->validar();

        if (empty($alertas)) {
            $resultado = $review->crear();

            if ($resultado['resultado']) {
                // Recalcular rating del asset
                $asset = AcademicAsset::find($assetId);
                if ($asset) {
                    $asset->recalculateRating();

                    // Log de reputación para el autor
                    ReputationLog::logEvent(
                        $asset->autor_id,
                        'rating',
                        (float)$rating * 0.2,
                        $assetId,
                        'Nueva reseña recibida (' . $rating . ' estrellas)'
                    );
                }
            }
        }

        header('Location: /marketplace/asset?id=' . $assetId . '&reviewed=1');
        exit;
    }
}
