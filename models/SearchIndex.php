<?php

declare(strict_types=1);

namespace Model;

/**
 * Clase SearchIndex
 * 
 * Motor de búsqueda global con FULLTEXT de MySQL.
 * Indexa assets, advisors y usuarios para búsqueda rápida.
 */
class SearchIndex extends ActiveRecord
{
    protected static string $tabla = 'search_index';
    protected static array $columnasDB = ['id', 'tipo', 'referencia_id', 'titulo', 'contenido', 'tags', 'faculty_id', 'career_id', 'category_id'];

    public ?int $id;
    public string $tipo;
    public int $referencia_id;
    public string $titulo;
    public string $contenido;
    public ?string $tags;
    public ?int $faculty_id;
    public ?int $career_id;
    public ?int $category_id;

    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->tipo = $args['tipo'] ?? 'asset';
        $this->referencia_id = (int)($args['referencia_id'] ?? 0);
        $this->titulo = $args['titulo'] ?? '';
        $this->contenido = $args['contenido'] ?? '';
        $this->tags = $args['tags'] ?? null;
        $this->faculty_id = isset($args['faculty_id']) ? (int)$args['faculty_id'] : null;
        $this->career_id = isset($args['career_id']) ? (int)$args['career_id'] : null;
        $this->category_id = isset($args['category_id']) ? (int)$args['category_id'] : null;
    }

    /**
     * Búsqueda FULLTEXT global con filtros opcionales
     * 
     * @param string $query Término de búsqueda
     * @param array  $filters Filtros opcionales: tipo, faculty_id, career_id, category_id
     * @param int    $limit Resultados máximos
     * @return array Resultados indexados
     */
    public static function globalSearch(string $query, array $filters = [], int $limit = 30): array
    {
        if (strlen(trim($query)) < 2) {
            return [];
        }

        $conditions = [];
        $params = [];
        $types = "";

        // FULLTEXT match
        $conditions[] = "MATCH(titulo, contenido, tags) AGAINST(? IN BOOLEAN MODE)";
        $searchQuery = '+' . str_replace(' ', ' +', trim($query)) . '*';
        $params[] = $searchQuery;
        $types .= "s";

        // Filtros
        if (!empty($filters['tipo'])) {
            $conditions[] = "tipo = ?";
            $params[] = $filters['tipo'];
            $types .= "s";
        }
        if (!empty($filters['faculty_id'])) {
            $conditions[] = "faculty_id = ?";
            $params[] = (int)$filters['faculty_id'];
            $types .= "i";
        }
        if (!empty($filters['career_id'])) {
            $conditions[] = "career_id = ?";
            $params[] = (int)$filters['career_id'];
            $types .= "i";
        }
        if (!empty($filters['category_id'])) {
            $conditions[] = "category_id = ?";
            $params[] = (int)$filters['category_id'];
            $types .= "i";
        }

        $where = implode(' AND ', $conditions);
        $sql = "SELECT *, MATCH(titulo, contenido, tags) AGAINST(? IN BOOLEAN MODE) AS relevance FROM " . static::$tabla . " WHERE {$where} ORDER BY relevance DESC LIMIT ?";
        
        // Add the search query again for the SELECT relevance column
        array_unshift($params, $searchQuery);
        $types = "s" . $types . "i";
        $params[] = $limit;

        $stmt = self::$db->prepare($sql);
        if ($types) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();

        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = $registro;
        }
        $stmt->close();
        return $array;
    }

    /**
     * Indexa un asset académico en la tabla de búsqueda
     */
    public static function indexAsset(AcademicAsset $asset): void
    {
        // Verificar si ya existe
        $existing = self::whereArray(['tipo' => 'asset', 'referencia_id' => (string)$asset->id]);

        if (!empty($existing)) {
            // Actualizar
            $entry = $existing[0];
            $entry->titulo = $asset->titulo;
            $entry->contenido = $asset->descripcion;
            $entry->faculty_id = $asset->faculty_id;
            $entry->career_id = $asset->career_id;
            $entry->category_id = $asset->category_id;
            $entry->actualizar();
        } else {
            // Crear nuevo
            $entry = new self([
                'tipo' => 'asset',
                'referencia_id' => $asset->id,
                'titulo' => $asset->titulo,
                'contenido' => $asset->descripcion,
                'faculty_id' => $asset->faculty_id,
                'career_id' => $asset->career_id,
                'category_id' => $asset->category_id,
            ]);
            $entry->crear();
        }
    }

    /**
     * Indexa un perfil de advisor
     */
    public static function indexAdvisor(AdvisorProfile $advisor, Usuario $usuario): void
    {
        $existing = self::whereArray(['tipo' => 'advisor', 'referencia_id' => (string)$advisor->id]);

        $tags = implode(', ', $advisor->getEspecialidadesArray());

        if (!empty($existing)) {
            $entry = $existing[0];
            $entry->titulo = $usuario->nombre . ' ' . $usuario->apellido;
            $entry->contenido = $advisor->descripcion;
            $entry->tags = $tags;
            $entry->faculty_id = $usuario->faculty_id ?? null;
            $entry->actualizar();
        } else {
            $entry = new self([
                'tipo' => 'advisor',
                'referencia_id' => $advisor->id,
                'titulo' => $usuario->nombre . ' ' . $usuario->apellido,
                'contenido' => $advisor->descripcion,
                'tags' => $tags,
                'faculty_id' => $usuario->faculty_id ?? null,
            ]);
            $entry->crear();
        }
    }

    /**
     * Sugerencias rápidas (autocomplete)
     */
    public static function quickSuggest(string $query, int $limit = 5): array
    {
        if (strlen(trim($query)) < 2) {
            return [];
        }

        $searchTerm = "%{$query}%";
        $sql = "SELECT titulo, tipo, referencia_id FROM " . static::$tabla . " WHERE titulo LIKE ? LIMIT ?";
        $stmt = self::$db->prepare($sql);
        $stmt->bind_param("si", $searchTerm, $limit);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $suggestions = [];
        while ($registro = $resultado->fetch_assoc()) {
            $suggestions[] = $registro;
        }
        $stmt->close();
        return $suggestions;
    }
}
