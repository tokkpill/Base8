<?php

declare(strict_types=1);

namespace Controllers;

use Model\AcademicAsset;
use Model\AssetCategory;
use Model\Faculty;
use Model\Career;
use Model\SearchIndex;
use MVC\Router;

/**
 * Clase SearchController
 * 
 * Motor de búsqueda global de alto rendimiento.
 * Conecta con MySQL FULLTEXT y filtros en tiempo real.
 */
class SearchController
{
    /**
     * Página principal de búsqueda con filtros
     */
    public static function index(Router $router): void
    {
        $faculties = Faculty::allOrdered();
        $categories = AssetCategory::all('ASC');

        $router->render('search/index', [
            'titulo' => 'Buscar — Base8',
            'faculties' => $faculties,
            'categories' => $categories,
        ]);
    }

    /**
     * Procesa los resultados de búsqueda
     */
    public static function results(Router $router): void
    {
        $keyword = s($_GET['q'] ?? '');
        $facultyId = (int)($_GET['faculty'] ?? 0);
        $careerId = (int)($_GET['career'] ?? 0);
        $categoryId = (int)($_GET['category'] ?? 0);
        $orderBy = s($_GET['order'] ?? 'relevance');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 12;
        $offset = ($page - 1) * $perPage;

        // Búsqueda avanzada
        $results = AcademicAsset::advancedSearch(
            $keyword, $facultyId, $careerId, $categoryId, $orderBy, $perPage, $offset
        );

        // Cargar relaciones
        foreach ($results as $asset) {
            $asset->autor = \Model\Usuario::find($asset->autor_id);
            $asset->category = AssetCategory::find($asset->category_id);
            $asset->faculty = Faculty::find($asset->faculty_id);
        }

        $totalResults = AcademicAsset::countAdvancedSearch($keyword, $facultyId, $careerId, $categoryId);
        $totalPages = (int)ceil($totalResults / $perPage);

        $faculties = Faculty::allOrdered();
        $categories = AssetCategory::all('ASC');

        // Carreras filtradas si hay facultad seleccionada
        $careers = $facultyId > 0 ? Career::getByFaculty($facultyId) : [];

        $router->render('search/results', [
            'titulo' => $keyword ? "'{$keyword}' — Resultados" : 'Explorar — Base8',
            'results' => $results,
            'keyword' => $keyword,
            'facultyId' => $facultyId,
            'careerId' => $careerId,
            'categoryId' => $categoryId,
            'orderBy' => $orderBy,
            'page' => $page,
            'totalResults' => $totalResults,
            'totalPages' => $totalPages,
            'faculties' => $faculties,
            'careers' => $careers,
            'categories' => $categories,
        ]);
    }

    /**
     * API JSON para sugerencias de búsqueda en tiempo real
     */
    public static function api(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $q = s($_GET['q'] ?? '');

        if (strlen($q) < 2) {
            echo json_encode([]);
            return;
        }

        $suggestions = SearchIndex::quickSuggest($q, 8);
        echo json_encode($suggestions);
    }

    /**
     * API para obtener carreras por facultad (para filtros dinámicos)
     */
    public static function apiCareers(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $facultyId = (int)($_GET['faculty_id'] ?? 0);
        
        if ($facultyId <= 0) {
            echo json_encode([]);
            return;
        }

        $careers = Career::getByFaculty($facultyId);
        $result = array_map(fn($c) => ['id' => $c->id, 'nombre' => $c->nombre], $careers);
        echo json_encode($result);
    }
}
