<?php

declare(strict_types=1);

namespace Model;

class Hora extends ActiveRecord {
    protected static string $tabla = 'horas';
    protected static array $columnasDB = ['id', 'hora'];

    public ?int $id;
    public ?string $hora;

    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->hora = $args['hora'] ?? '';
    }
}