<?php

declare(strict_types=1);

namespace Model;

/**
 * Clase Evento
 * 
 * Representa una conferencia o taller dentro de la convención, vinculando
 * la categoría, el día, la hora y el ponente a cargo.
 */
class Evento extends ActiveRecord
{

    public ?Categoria $categoria = null;
    public ?Dia $dia = null;
    public ?Hora $hora = null;
    public ?Ponente $ponente = null;



    /** @var string Nombre de la tabla en la base de datos */
    protected static string $tabla = 'eventos';

    /** @var array Columnas de la tabla para mapeo y sanitización */
    protected static array $columnasDB = ['id', 'nombre', 'descripcion', 'disponibles', 'categoria_id', 'dia_id', 'hora_id', 'ponente_id'];

    /** @var int|null ID único del evento */
    public ?int $id;
    /** @var string Título del evento */
    public string $nombre;
    /** @var string Resumen detallado de qué trata el evento */
    public string $descripcion;
    /** @var int Cantidad de cupos disponibles para el evento */
    public int $disponibles;
    /** @var int ID de la categoría (conferencia o workshop) */
    public int $categoria_id;
    /** @var int ID del día en que se realiza el evento */
    public int $dia_id;
    /** @var int ID de la hora en que se realiza el evento */
    public int $hora_id;
    /** @var int ID del ponente asignado al evento */
    public int $ponente_id;

    /**
     * Constructor de la clase Evento
     * 
     * @param array $args Datos iniciales para el evento
     */
    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->descripcion = $args['descripcion'] ?? '';
        $this->disponibles = (int)($args['disponibles'] ?? 0);
        $this->categoria_id = (int)($args['categoria_id'] ?? 0);
        $this->dia_id = (int)($args['dia_id'] ?? 0);
        $this->hora_id = (int)($args['hora_id'] ?? 0);
        $this->ponente_id = (int)($args['ponente_id'] ?? 0);
    }

    /**
     * Valida que el evento tenga toda la información necesaria para ser registrado
     * 
     * @return array Alertas de validación generadas
     */
    public function validar(): array
    {
        if (!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if (!$this->descripcion) {
            self::$alertas['error'][] = 'La descripción es Obligatoria';
        }
        if (!$this->categoria_id  || !filter_var($this->categoria_id, FILTER_VALIDATE_INT)) {
            self::$alertas['error'][] = 'Elige una Categoría';
        }
        if (!$this->dia_id  || !filter_var($this->dia_id, FILTER_VALIDATE_INT)) {
            self::$alertas['error'][] = 'Elige el Día del evento';
        }
        if (!$this->hora_id  || !filter_var($this->hora_id, FILTER_VALIDATE_INT)) {
            self::$alertas['error'][] = 'Elige la hora del evento';
        }
        if (!$this->disponibles  || !filter_var($this->disponibles, FILTER_VALIDATE_INT)) {
            self::$alertas['error'][] = 'Añade una cantidad de Lugares Disponibles';
        }
        if (!$this->ponente_id || !filter_var($this->ponente_id, FILTER_VALIDATE_INT)) {
            self::$alertas['error'][] = 'Selecciona la persona encargada del evento';
        }

        return self::$alertas;
    }
}
