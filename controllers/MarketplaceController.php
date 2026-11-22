<?php

declare(strict_types=1);

namespace Controllers;

use Model\AcademicAsset;
use Model\AssetCategory;
use Model\AssetReview;
use Model\Faculty;
use Model\Career;
use Model\MarketplaceTransaction;
use Model\ReputationLog;
use Model\SearchIndex;
use Model\Usuario;
use MVC\Router;

/**
 * Clase MarketplaceController
 * 
 * Gestiona el marketplace P2P de recursos académicos.
 * Navegación, subida, compra y gestión de assets digitales.
 */
class MarketplaceController
{
    /**
     * Marketplace principal — grid de assets
     */
    public static function index(Router $router): void
    {
        $categorySlug = s($_GET['category'] ?? '');
        $facultyId = (int)($_GET['faculty'] ?? 0);
        $orderBy = s($_GET['order'] ?? 'newest');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        $categoryId = 0;
        if ($categorySlug) {
            $category = AssetCategory::findBySlug($categorySlug);
            $categoryId = $category ? $category->id : 0;
        }

        $assets = AcademicAsset::advancedSearch('', $facultyId, 0, $categoryId, $orderBy, $perPage, $offset);
        $total = AcademicAsset::countAdvancedSearch('', $facultyId, 0, $categoryId);
        $totalPages = (int)ceil($total / $perPage);

        // Cargar relaciones
        foreach ($assets as $asset) {
            $asset->autor = Usuario::find($asset->autor_id);
            $asset->category = AssetCategory::find($asset->category_id);
        }

        $featured = AcademicAsset::getFeatured(3);
        foreach ($featured as $feat) {
            $feat->autor = Usuario::find($feat->autor_id);
        }

        $categories = AssetCategory::all('ASC');
        $faculties = Faculty::allOrdered();

        $router->render('marketplace/index', [
            'titulo' => 'Marketplace — Base8',
            'assets' => $assets,
            'featured' => $featured,
            'categories' => $categories,
            'faculties' => $faculties,
            'categorySlug' => $categorySlug,
            'facultyId' => $facultyId,
            'orderBy' => $orderBy,
            'page' => $page,
            'total' => $total,
            'totalPages' => $totalPages,
        ]);
    }

    /**
     * Detalle de un asset individual con reseñas
     */
    public static function show(Router $router): void
    {
        $id = (int)($_GET['id'] ?? 0);
        if (!$id) {
            header('Location: /marketplace');
            exit;
        }

        $asset = AcademicAsset::find($id);
        if (!$asset || $asset->estado !== 'publicado') {
            header('Location: /marketplace');
            exit;
        }

        $asset->autor = Usuario::find($asset->autor_id);
        $asset->category = AssetCategory::find($asset->category_id);
        $asset->faculty = Faculty::find($asset->faculty_id);
        if ($asset->career_id) {
            $asset->career = Career::find($asset->career_id);
        }

        $reviews = AssetReview::getByAsset($id);

        // Check si el usuario actual ya compró este asset
        $hasPurchased = false;
        $hasReviewed = false;
        if (is_auth()) {
            $userId = (int)($_SESSION['id'] ?? 0);
            $hasPurchased = MarketplaceTransaction::hasPurchased($userId, $id);
            $hasReviewed = AssetReview::hasReviewed($userId, $id);
        }

        // Assets similares (misma categoría)
        $similar = AcademicAsset::advancedSearch('', 0, 0, $asset->category_id, 'rating', 4, 0);
        $similar = array_filter($similar, fn($a) => $a->id !== $asset->id);
        $similar = array_slice($similar, 0, 3);
        foreach ($similar as $sim) {
            $sim->autor = Usuario::find($sim->autor_id);
        }

        $router->render('marketplace/show', [
            'titulo' => $asset->titulo . ' — Base8',
            'asset' => $asset,
            'reviews' => $reviews,
            'hasPurchased' => $hasPurchased,
            'hasReviewed' => $hasReviewed,
            'similar' => $similar,
        ]);
    }

    /**
     * Formulario para subir un nuevo asset
     */
    public static function upload(Router $router): void
    {
        if (!is_auth()) {
            header('Location: /login');
            exit;
        }

        $alertas = [];
        $asset = new AcademicAsset();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                $alertas['error'][] = 'Token de seguridad inválido';
            } else {
                $asset->sincronizar($_POST);
                $asset->autor_id = (int)$_SESSION['id'];

                // Manejar archivo
                if (!empty($_FILES['archivo']['name'])) {
                    $uploadDir = __DIR__ . '/../public/uploads/assets/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $ext = pathinfo($_FILES['archivo']['name'], PATHINFO_EXTENSION);
                    $allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'zip', 'rar', 'txt', 'md'];
                    
                    if (!in_array(strtolower($ext), $allowed)) {
                        $alertas['error'][] = 'Formato de archivo no permitido';
                    } else {
                        $filename = uniqid('asset_') . '.' . $ext;
                        if (move_uploaded_file($_FILES['archivo']['tmp_name'], $uploadDir . $filename)) {
                            $asset->archivo = $filename;
                            $asset->formato = strtolower($ext);
                            $asset->tamano_bytes = (int)$_FILES['archivo']['size'];
                        } else {
                            $alertas['error'][] = 'Error al subir el archivo';
                        }
                    }
                }

                $alertas = $asset->validar();

                if (empty($alertas)) {
                    $resultado = $asset->crear();

                    if ($resultado['resultado']) {
                        // Indexar en búsqueda
                        $asset->id = $resultado['id'];
                        SearchIndex::indexAsset($asset);

                        // Registrar contribución
                        ReputationLog::logEvent(
                            $asset->autor_id,
                            'contribucion',
                            1.5,
                            $resultado['id'],
                            'Nuevo recurso publicado: ' . $asset->titulo
                        );

                        header('Location: /marketplace/asset?id=' . $resultado['id']);
                        exit;
                    }
                }
            }
        }

        $categories = AssetCategory::all('ASC');
        $faculties = Faculty::allOrdered();

        $router->render('marketplace/upload', [
            'titulo' => 'Publicar Recurso — Base8',
            'asset' => $asset,
            'categories' => $categories,
            'faculties' => $faculties,
            'alertas' => $alertas,
        ]);
    }

    /**
     * Procesar compra de un asset
     */
    public static function purchase(Router $router): void
    {
        if (!is_auth()) {
            header('Location: /login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!verify_csrf()) {
                header('Location: /marketplace');
                exit;
            }

            $assetId = (int)($_POST['asset_id'] ?? 0);
            $userId = (int)$_SESSION['id'];

            $resultado = MarketplaceTransaction::initiatePurchase($assetId, $userId);

            if ($resultado['resultado'] ?? false) {
                header('Location: /marketplace/asset?id=' . $assetId . '&purchased=1');
            } else {
                header('Location: /marketplace/asset?id=' . $assetId . '&error=' . urlencode($resultado['error'] ?? 'Error'));
            }
            exit;
        }

        header('Location: /marketplace');
        exit;
    }

    /**
     * Mis recursos publicados
     */
    public static function myAssets(Router $router): void
    {
        if (!is_auth()) {
            header('Location: /login');
            exit;
        }

        $userId = (int)$_SESSION['id'];
        $assets = AcademicAsset::getByAutor($userId);
        
        foreach ($assets as $asset) {
            $asset->category = AssetCategory::find($asset->category_id);
        }

        $totalRevenue = MarketplaceTransaction::totalRevenue($userId);

        $router->render('marketplace/my-assets', [
            'titulo' => 'Mis Recursos — Base8',
            'assets' => $assets,
            'totalRevenue' => $totalRevenue,
        ]);
    }

    /**
     * Mis compras
     */
    public static function myPurchases(Router $router): void
    {
        if (!is_auth()) {
            header('Location: /login');
            exit;
        }

        $userId = (int)$_SESSION['id'];
        $transactions = MarketplaceTransaction::getByComprador($userId);
        
        foreach ($transactions as $tx) {
            $tx->asset = AcademicAsset::find($tx->asset_id);
            $tx->vendedor = Usuario::find($tx->vendedor_id);
        }

        $router->render('marketplace/my-purchases', [
            'titulo' => 'Mis Compras — Base8',
            'transactions' => $transactions,
        ]);
    }
}
