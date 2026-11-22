<?php

declare(strict_types=1);

namespace Model;

/**
 * Clase Faculty
 * 
 * Representa una facultad académica de la UAEMex.
 * Tabla catálogo usada para filtrados y organización.
 */
class Faculty extends ActiveRecord
{
    protected static string $tabla = 'faculties';
    protected static array $columnasDB = ['id', 'nombre', 'codigo'];

    public ?int $id;
    public string $nombre;
    public string $codigo;

    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->codigo = $args['codigo'] ?? '';
    }

    /**
     * Obtiene todas las facultades ordenadas por nombre
     */
    public static function allOrdered(): array
    {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY nombre ASC";
        return self::consultarSQL($query);
    }
}
