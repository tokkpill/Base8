<?php

declare(strict_types=1);

namespace Model;

class EventosRegistros extends ActiveRecord {
    protected static string $tabla = 'eventos_registros';
    protected static array $columnasDB = ['id', 'evento_id', 'registro_id'];

    public ?int $id;
    public int $evento_id;
    public int $registro_id;

    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->evento_id = (int)($args['evento_id'] ?? 0);
        $this->registro_id = (int)($args['registro_id'] ?? 0);
    }
}