<?php

declare(strict_types=1);

namespace Model;

/**
 * Clase AssetCategory
 * 
 * Categorías de recursos académicos (Apuntes, Guías, Plantillas, etc.)
 */
class AssetCategory extends ActiveRecord
{
    protected static string $tabla = 'asset_categories';
    protected static array $columnasDB = ['id', 'nombre', 'slug', 'icono'];

    public ?int $id;
    public string $nombre;
    public string $slug;
    public string $icono;

    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->slug = $args['slug'] ?? '';
        $this->icono = $args['icono'] ?? 'fa-file';
    }

    /**
     * Busca por slug
     */
    public static function findBySlug(string $slug): ?static
    {
        return static::where('slug', $slug);
    }
}
