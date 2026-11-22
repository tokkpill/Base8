<?php

declare(strict_types=1);

namespace Model;

/**
 * Clase AcademicAsset
 * 
 * Representa un recurso académico disponible en el marketplace.
 * Incluye propiedades para búsqueda, filtrado, rating y transacciones.
 */
class AcademicAsset extends ActiveRecord
{
    protected static string $tabla = 'academic_assets';
    protected static array $columnasDB = [
        'id', 'titulo', 'descripcion', 'category_id', 'faculty_id', 'career_id',
        'autor_id', 'precio', 'archivo', 'archivo_preview', 'formato', 'tamano_bytes',
        'descargas', 'rating_promedio', 'total_ratings', 'estado', 'destacado'
    ];

    public ?int $id;
    public string $titulo;
    public string $descripcion;
    public int $category_id;
    public int $faculty_id;
    public ?int $career_id;
    public int $autor_id;
    public float $precio;
    public string $archivo;
    public ?string $archivo_preview;
    public string $formato;
    public int $tamano_bytes;
    public int $descargas;
    public float $rating_promedio;
    public int $total_ratings;
    public string $estado;
    public int $destacado;

    /** @var Usuario|null Autor del recurso */
    public ?Usuario $autor = null;
    /** @var AssetCategory|null Categoría del recurso */
    public ?AssetCategory $category = null;
    /** @var Faculty|null Facultad del recurso */
    public ?Faculty $faculty = null;
    /** @var Career|null Carrera del recurso */
    public ?Career $career = null;

    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->titulo = $args['titulo'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->category_id = (int)($args['category_id'] ?? 0);
        $this->faculty_id = (int)($args['faculty_id'] ?? 0);
        $this->career_id = isset($args['career_id']) ? (int)$args['career_id'] : null;
        $this->autor_id = (int)($args['autor_id'] ?? 0);
        $this->precio = (float)($args['precio'] ?? 0.00);
        $this->archivo = $args['archivo'] ?? '';
        $this->archivo_preview = $args['archivo_preview'] ?? null;
        $this->formato = $args['formato'] ?? 'pdf';
        $this->tamano_bytes = (int)($args['tamano_bytes'] ?? 0);
        $this->descargas = (int)($args['descargas'] ?? 0);
        $this->rating_promedio = (float)($args['rating_promedio'] ?? 0.00);
        $this->total_ratings = (int)($args['total_ratings'] ?? 0);
        $this->estado = $args['estado'] ?? 'publicado';
        $this->destacado = (int)($args['destacado'] ?? 0);
    }

    /**
     * Valida campos obligatorios del asset
     */
    public function validar(): array
    {
        if (!$this->titulo) {
            self::$alertas['error'][] = 'El título es obligatorio';
        }
        if (!$this->descripcion) {
            self::$alertas['error'][] = 'La descripción es obligatoria';
        }
        if (!$this->category_id) {
            self::$alertas['error'][] = 'Selecciona una categoría';
        }
        if (!$this->faculty_id) {
            self::$alertas['error'][] = 'Selecciona una facultad';
        }
        if ($this->precio < 0) {
            self::$alertas['error'][] = 'El precio no puede ser negativo';
        }
        return self::$alertas;
    }

    /**
     * Búsqueda por keyword en título y descripción
     */
    public static function searchByKeyword(string $keyword, int $limit = 20, int $offset = 0): array
    {
        $searchTerm = "%{$keyword}%";
        $query = "SELECT * FROM " . static::$tabla . " WHERE estado = 'publicado' AND (titulo LIKE ? OR descripcion LIKE ?) ORDER BY rating_promedio DESC, descargas DESC LIMIT ? OFFSET ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("ssii", $searchTerm, $searchTerm, $limit, $offset);
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
     * Filtra por facultad
     */
    public static function filterByFaculty(int $facultyId, int $limit = 20): array
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE estado = 'publicado' AND faculty_id = ? ORDER BY created_at DESC LIMIT ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("ii", $facultyId, $limit);
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
     * Obtiene los assets mejor calificados
     */
    public static function getTopRated(int $limit = 10): array
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE estado = 'publicado' AND total_ratings > 0 ORDER BY rating_promedio DESC, total_ratings DESC LIMIT ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $limit);
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
     * Assets destacados para el dashboard
     */
    public static function getFeatured(int $limit = 6): array
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE estado = 'publicado' AND destacado = 1 ORDER BY created_at DESC LIMIT ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $limit);
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
     * Obtiene assets por autor
     */
    public static function getByAutor(int $autorId): array
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE autor_id = ? ORDER BY created_at DESC";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $autorId);
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
     * Incrementa el contador de descargas
     */
    public function incrementDownloads(): bool
    {
        $query = "UPDATE " . static::$tabla . " SET descargas = descargas + 1 WHERE id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $this->id);
        $resultado = $stmt->execute();
        $stmt->close();
        if ($resultado) {
            $this->descargas++;
        }
        return $resultado;
    }

    /**
     * Recalcula el rating promedio desde la tabla de reviews
     */
    public function recalculateRating(): void
    {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM asset_reviews WHERE asset_id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $this->id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $data = $resultado->fetch_assoc();
        $stmt->close();

        $this->rating_promedio = (float)($data['avg_rating'] ?? 0);
        $this->total_ratings = (int)($data['total'] ?? 0);
        $this->actualizar();
    }

    /**
     * Búsqueda avanzada con múltiples filtros
     */
    public static function advancedSearch(
        string $keyword = '',
        int $facultyId = 0,
        int $careerId = 0,
        int $categoryId = 0,
        string $orderBy = 'relevance',
        int $limit = 20,
        int $offset = 0
    ): array {
        $conditions = ["estado = 'publicado'"];
        $params = [];
        $types = "";

        if ($keyword) {
            $conditions[] = "(titulo LIKE ? OR descripcion LIKE ?)";
            $searchTerm = "%{$keyword}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }
        if ($facultyId > 0) {
            $conditions[] = "faculty_id = ?";
            $params[] = $facultyId;
            $types .= "i";
        }
        if ($careerId > 0) {
            $conditions[] = "career_id = ?";
            $params[] = $careerId;
            $types .= "i";
        }
        if ($categoryId > 0) {
            $conditions[] = "category_id = ?";
            $params[] = $categoryId;
            $types .= "i";
        }

        $orderClause = match ($orderBy) {
            'price_asc' => 'precio ASC',
            'price_desc' => 'precio DESC',
            'rating' => 'rating_promedio DESC',
            'downloads' => 'descargas DESC',
            'newest' => 'created_at DESC',
            default => 'rating_promedio DESC, descargas DESC'
        };

        $where = implode(' AND ', $conditions);
        $query = "SELECT * FROM " . static::$tabla . " WHERE {$where} ORDER BY {$orderClause} LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        $types .= "ii";

        $stmt = self::$db->prepare($query);
        if ($types) {
            $stmt->bind_param($types, ...$params);
        }
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
     * Cuenta resultados de búsqueda avanzada
     */
    public static function countAdvancedSearch(
        string $keyword = '',
        int $facultyId = 0,
        int $careerId = 0,
        int $categoryId = 0
    ): int {
        $conditions = ["estado = 'publicado'"];
        $params = [];
        $types = "";

        if ($keyword) {
            $conditions[] = "(titulo LIKE ? OR descripcion LIKE ?)";
            $searchTerm = "%{$keyword}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $types .= "ss";
        }
        if ($facultyId > 0) {
            $conditions[] = "faculty_id = ?";
            $params[] = $facultyId;
            $types .= "i";
        }
        if ($careerId > 0) {
            $conditions[] = "career_id = ?";
            $params[] = $careerId;
            $types .= "i";
        }
        if ($categoryId > 0) {
            $conditions[] = "category_id = ?";
            $params[] = $categoryId;
            $types .= "i";
        }

        $where = implode(' AND ', $conditions);
        $query = "SELECT COUNT(*) FROM " . static::$tabla . " WHERE {$where}";

        $stmt = self::$db->prepare($query);
        if ($types) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();
        $total = $resultado->fetch_array();
        $stmt->close();

        return (int)array_shift($total);
    }
}
