<?php 

declare(strict_types=1);

namespace Model;

class Regalo extends ActiveRecord {
    protected static string $tabla = 'regalos';
    protected static array $columnasDB = ['id', 'nombre'];

    public ?int $id;
    public string $nombre;

    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->nombre = $args['nombre'] ?? '';
    }
}