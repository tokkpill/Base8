<?php

declare(strict_types=1);

namespace Model;

/**
 * Clase AdvisorProfile
 * 
 * Perfil extendido para usuarios con rol de "Advisor".
 * Estilo LinkedIn con especialidades, métricas y disponibilidad.
 */
class AdvisorProfile extends ActiveRecord
{
    protected static string $tabla = 'advisor_profiles';
    protected static array $columnasDB = [
        'id', 'usuario_id', 'especialidades', 'descripcion', 'tarifa_hora',
        'disponibilidad', 'estudiantes_ayudados', 'rating_promedio', 'total_sesiones', 'activo'
    ];

    public ?int $id;
    public int $usuario_id;
    public string $especialidades;
    public string $descripcion;
    public float $tarifa_hora;
    public ?string $disponibilidad;
    public int $estudiantes_ayudados;
    public float $rating_promedio;
    public int $total_sesiones;
    public int $activo;

    /** @var Usuario|null Usuario relacionado */
    public ?Usuario $usuario = null;

    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->usuario_id = (int)($args['usuario_id'] ?? 0);
        $this->especialidades = $args['especialidades'] ?? '[]';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->tarifa_hora = (float)($args['tarifa_hora'] ?? 0.00);
        $this->disponibilidad = $args['disponibilidad'] ?? null;
        $this->estudiantes_ayudados = (int)($args['estudiantes_ayudados'] ?? 0);
        $this->rating_promedio = (float)($args['rating_promedio'] ?? 0.00);
        $this->total_sesiones = (int)($args['total_sesiones'] ?? 0);
        $this->activo = (int)($args['activo'] ?? 1);
    }

    /**
     * Valida el perfil de advisor
     */
    public function validar(): array
    {
        if (!$this->descripcion) {
            self::$alertas['error'][] = 'La descripción es obligatoria';
        }
        if (!$this->especialidades || $this->especialidades === '[]') {
            self::$alertas['error'][] = 'Agrega al menos una especialidad';
        }
        if ($this->tarifa_hora < 0) {
            self::$alertas['error'][] = 'La tarifa no puede ser negativa';
        }
        return self::$alertas;
    }

    /**
     * Obtiene advisor por usuario ID
     */
    public static function getByUserId(int $userId): ?static
    {
        return static::where('usuario_id', (string)$userId);
    }

    /**
     * Obtiene todos los advisors activos con datos del usuario
     */
    public static function getActive(int $limit = 20): array
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE activo = 1 ORDER BY rating_promedio DESC, estudiantes_ayudados DESC LIMIT ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $advisor = static::crearObjeto($registro);
            $advisor->usuario = Usuario::find($advisor->usuario_id);
            $array[] = $advisor;
        }
        $stmt->close();
        return $array;
    }

    /**
     * Top advisors por rating
     */
    public static function getTopAdvisors(int $limit = 5): array
    {
        return self::getActive($limit);
    }

    /**
     * Obtiene las especialidades como array
     */
    public function getEspecialidadesArray(): array
    {
        $decoded = json_decode($this->especialidades, true);
        return is_array($decoded) ? $decoded : [];
    }
}
