<?php 

declare(strict_types=1);

namespace Model;

class Registro extends ActiveRecord {
    protected static string $tabla = 'registros';
    protected static array $columnasDB = ['id', 'paquete_id', 'pago_id', 'token', 'usuario_id', 'regalo_id'];

    public ?int $id;
    public int $paquete_id;
    public string $pago_id;
    public string $token;
    public int $usuario_id;
    public int $regalo_id;

    /** @var Usuario|null Objeto usuario relacionado */
    public ?Usuario $usuario;
    /** @var Paquete|null Objeto paquete relacionado */
    public ?Paquete $paquete;


    public function __construct(array $args = [])
    {
        $this->id = $args['id'] ?? null;
        $this->paquete_id = (int)($args['paquete_id'] ?? 0);
        $this->pago_id = $args['pago_id'] ?? '';
        $this->token = $args['token'] ?? '';
        $this->usuario_id = (int)($args['usuario_id'] ?? 0);
        $this->regalo_id = (int)($args['regalo_id'] ?? 1);
    }
}