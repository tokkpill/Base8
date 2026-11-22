<?php

declare(strict_types=1);

namespace Model;

class EventoHorario extends ActiveRecord {
    protected static string $tabla = 'eventos';
    protected static array $columnasDB = ['id', 'categoria_id', 'dia_id', 'hora_id'];

    public ?int $id;
    public int $categoria_id;
    public int $dia_id;
    public int $hora_id;

    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->categoria_id = (int)($args['categoria_id'] ?? 0);
        $this->dia_id = (int)($args['dia_id'] ?? 0);
        $this->hora_id = (int)($args['hora_id'] ?? 0);
    }
}