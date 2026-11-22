<?php

declare(strict_types=1);

namespace Model;

/**
 * Clase Career
 * 
 * Representa una carrera académica vinculada a una facultad.
 */
class Career extends ActiveRecord
{
    protected static string $tabla = 'careers';
    protected static array $columnasDB = ['id', 'nombre', 'faculty_id'];

    public ?int $id;
    public string $nombre;
    public int $faculty_id;

    /** @var Faculty|null Relación con la facultad */
    public ?Faculty $faculty = null;

    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->faculty_id = (int)($args['faculty_id'] ?? 0);
    }

    /**
     * Obtiene las carreras de una facultad específica
     */
    public static function getByFaculty(int $facultyId): array
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE faculty_id = ? ORDER BY nombre ASC";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $facultyId);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }
        $stmt->close();
        return $array;
    }
}
