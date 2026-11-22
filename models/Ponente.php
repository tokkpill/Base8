<?php 

declare(strict_types=1);

namespace Model;

/**
 * Clase Ponente
 * 
 * Representa a un conferencista o ponente del evento, incluyendo su información 
 * biográfica, redes sociales y áreas de experiencia (tags).
 */
class Ponente extends ActiveRecord {
    /** @var string Nombre de la tabla en la base de datos */
    protected static string $tabla = 'ponentes';

    /** @var array Columnas de la tabla para mapeo y sanitización */
    protected static array $columnasDB = ['id', 'nombre', 'apellido', 'ciudad', 'pais', 'imagen', 'tags', 'redes'];

    /** @var int|null ID único del ponente */
    public ?int $id;
    /** @var string Nombre del ponente */
    public string $nombre;
    /** @var string Apellido del ponente */
    public string $apellido;
    /** @var string Ciudad de residencia */
    public string $ciudad;
    /** @var string País de residencia */
    public string $pais;
    /** @var string Nombre del archivo de imagen del ponente */
    public string $imagen;
    /** @var string Áreas de especialidad concatenadas (tags) */
    public string $tags;
    /** @var string JSON con los enlaces a redes sociales */
    public string $redes;
    /** @var string|null Propiedad temporal para manejar la imagen previa en ediciones */
    public ?string $imagen_actual;


    /**
     * Constructor de la clase Ponente
     * 
     * @param array $args Datos iniciales para el ponente
     */
    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
        $this->apellido = $args['apellido'] ?? '';
        $this->ciudad = $args['ciudad'] ?? '';
        $this->pais = $args['pais'] ?? '';
        $this->imagen = $args['imagen'] ?? '';
        $this->tags = $args['tags'] ?? '';
        $this->redes = $args['redes'] ?? '';
    }

    /**
     * Valida la información del ponente para su registro o actualización
     * 
     * @return array Alertas de validación generadas
     */
    public function validar(): array {
        if(!$this->nombre) {
            self::$alertas['error'][] = 'El Nombre es Obligatorio';
        }
        if(!$this->apellido) {
            self::$alertas['error'][] = 'El Apellido es Obligatorio';
        }
        if(!$this->ciudad) {
            self::$alertas['error'][] = 'El Campo Ciudad es Obligatorio';
        }
        if(!$this->pais) {
            self::$alertas['error'][] = 'El Campo País es Obligatorio';
        }
        if(!$this->imagen) {
            self::$alertas['error'][] = 'La imagen es obligatoria';
        }
        if(!$this->tags) {
            self::$alertas['error'][] = 'El Campo áreas es obligatorio';
        }
    
        return self::$alertas;
    }
}