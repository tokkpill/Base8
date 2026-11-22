<?php

declare(strict_types=1);

namespace Model;

/**
 * Clase ActiveRecord
 * 
 * Clase base que implementa el patrón Active Record para la interacción con la base de datos.
 * Proporciona métodos CRUD y utilidades de consulta heredables por otros modelos.
 */
class ActiveRecord
{

    /** @var \mysqli Conexión a la base de datos */
    protected static \mysqli $db;

    /** @var string Nombre de la tabla en la base de datos */
    protected static string $tabla = '';

    /** @var array Columnas de la tabla para mapeo y sanitización */
    protected static array $columnasDB = [];

    /** @var array Almacena mensajes de error o alertas */
    protected static array $alertas = [];

    /**
     * Establece la conexión a la base de datos
     * 
     * @param \mysqli $database Objeto de conexión a la base de datos
     * @return void
     */
    public static function setDB(\mysqli $database): void
    {
        self::$db = $database;
    }

    /**
     * Agrega una alerta a la lista de mensajes
     * 
     * @param string $tipo El tipo de alerta (error, exito, etc)
     * @param string $mensaje El mensaje a mostrar
     * @return void
     */
    public static function setAlerta(string $tipo, string $mensaje): void
    {
        static::$alertas[$tipo][] = $mensaje;
    }

    /**
     * Obtiene todas las alertas registradas
     * 
     * @return array Mapa de alertas por tipo
     */
    public static function getAlertas(): array
    {
        return static::$alertas;
    }

    /**
     * Realiza la validación del modelo (debe ser sobreescrito por los hijos)
     * 
     * @return array Las alertas generadas durante la validación
     */
    public function validar(): array
    {
        static::$alertas = [];
        return static::$alertas;
    }

    /**
     * Ejecuta una consulta SQL genérica (Usa con precaución, prefiere métodos específicos)
     * 
     * @param string $query La consulta SQL a ejecutar
     * @return array Arreglo de instancias del modelo
     */
    public static function consultarSQL(string $query): array
    {
        // Consultar la base de datos
        $resultado = self::$db->query($query);

        // Iterar los resultados
        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }

        // liberar la memoria
        $resultado->free();

        // retornar los resultados
        return $array;
    }

    /**
     * Crea una instancia del modelo a partir de un registro de la base de datos
     * 
     * @param array $registro Datos provenientes de fetch_assoc
     * @return static Nueva instancia del modelo con los datos cargados
     */
    protected static function crearObjeto(array $registro): static
    {
        $objeto = new static;

        foreach ($registro as $key => $value) {
            if (property_exists($objeto, $key)) {

                // 🔥 convertir valores numéricos automáticamente
                if (is_numeric($value)) {
                    $value = (int) $value;
                }

                $objeto->$key = $value;
            }
        }

        return $objeto;
    }


    /**
     * Identifica los atributos que corresponden a las columnas de la base de datos
     * 
     * @return array Mapa de columna => valor
     */
    public function atributos(): array
    {
        $atributos = [];
        foreach (static::$columnasDB as $columna) {
            if ($columna === 'id') continue;
            $atributos[$columna] = $this->$columna;
        }
        return $atributos;
    }

    /**
     * Escapa y sanitiza los atributos antes de interactuar con la BD
     * 
     * @return array Atributos sanitizados
     */
    public function sanitizarAtributos(): array
    {
        $atributos = $this->atributos();
        $sanitizado = [];
        foreach ($atributos as $key => $value) {
            $sanitizado[$key] = self::$db->escape_string((string)$value);
        }
        return $sanitizado;
    }

    /**
     * Sincroniza el objeto en memoria con un arreglo de datos
     * 
     * @param array $args Datos a sincronizar con el objeto
     * @return void
     */
    public function sincronizar(array $args = []): void
    {
        foreach (static::$columnasDB as $columna) {

            if (!isset($args[$columna])) {
                continue;
            }

            $value = $args[$columna];

            if (is_numeric($value)) {
                $value = (int) $value;
            }

            $this->$columna = $value;
        }
    }


    /**
     * Guarda el registro actual (crea uno nuevo o actualiza el existente)
     * 
     * @return array|bool Resultado de la operación
     */
    public function guardar(): array|bool
    {
        if (!is_null($this->id)) {
            // actualizar
            return $this->actualizar();
        } else {
            // Creando un nuevo registro
            return $this->crear();
        }
    }

    /**
     * Obtiene todos los registros de la tabla
     * 
     * @param string $orden El orden de los resultados (ASC/DESC)
     * @return array Todas las instancias del modelo
     */
    public static function all(string $orden = 'DESC'): array
    {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY id " . ($orden === 'ASC' ? 'ASC' : 'DESC');
        return self::consultarSQL($query);
    }

    /**
     * Busca un registro por su ID único usando sentencias preparadas
     * 
     * @param int|string $id Identificador del registro
     * @return static|null La instancia encontrada o null
     */
    public static function find(int|string $id): ?static
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE id = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("s", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $registro = $resultado->fetch_assoc();
        $stmt->close();

        return $registro ? static::crearObjeto($registro) : null;
    }

    /**
     * Obtiene una cantidad limitada de registros
     * 
     * @param int $limite Cantidad máxima de registros a retornar
     * @return array Arreglo de instancias del modelo
     */
    public static function get(int $limite): array
    {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY id DESC LIMIT ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }
        $stmt->close();
        return $array;
    }

    /**
     * Obtiene registros paginados
     * 
     * @param int $por_pagina Cantidad de registros por página
     * @param int $offset Punto de inicio para la consulta
     * @return array Arreglo de instancias para la página actual
     */
    public static function paginar(int $por_pagina, int $offset): array
    {
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY id DESC LIMIT ? OFFSET ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("ii", $por_pagina, $offset);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }
        $stmt->close();
        return $array;
    }

    /**
     * Busca un registro por una columna y valor específicos usando sentencias preparadas
     * 
     * @param string $columna Nombre de la columna
     * @param mixed $valor Valor a buscar
     * @return static|null Primera coincidencia encontrada o null
     */
    public static function where(string $columna, mixed $valor): ?static
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE {$columna} = ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("s", $valor);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $registro = $resultado->fetch_assoc();
        $stmt->close();

        return $registro ? static::crearObjeto($registro) : null;
    }

    /**
     * Retorna los registros ordenados por una columna específica
     * 
     * @param string $columna Columna por la cual ordenar
     * @param string $orden Dirección del orden (ASC/DESC)
     * @return array Arreglo de instancias ordenadas
     */
    public static function ordenar(string $columna, string $orden): array
    {
        $orden = ($orden === 'ASC' ? 'ASC' : 'DESC');
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY {$columna} {$orden}";
        return self::consultarSQL($query);
    }

    /**
     * Retorna los registros ordenados y con un límite específico
     * 
     * @param string $columna Columna por la cual ordenar
     * @param string $orden Dirección del orden (ASC/DESC)
     * @param int $limite Cantidad máxima de registros
     * @return array Arreglo de instancias
     */
    public static function ordenarLimite(string $columna, string $orden, int $limite): array
    {
        $orden = ($orden === 'ASC' ? 'ASC' : 'DESC');
        $query = "SELECT * FROM " . static::$tabla . " ORDER BY {$columna} {$orden} LIMIT ?";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("i", $limite);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $array = [];
        while ($registro = $resultado->fetch_assoc()) {
            $array[] = static::crearObjeto($registro);
        }
        $stmt->close();
        return $array;
    }

    /**
     * Busca múltiples registros que cumplan con varios criterios (WHERE AND)
     * 
     * @param array $array Mapa de columna => valor para los criterios de búsqueda
     * @return array Arreglo de instancias que coinciden
     */
    public static function whereArray(array $array = []): array
    {
        $query = "SELECT * FROM " . static::$tabla . " WHERE ";
        $valores = [];
        $tipos = "";

        foreach ($array as $key => $value) {
            $query .= " {$key} = ? ";
            if ($key !== array_key_last($array)) {
                $query .= " AND ";
            }
            $valores[] = $value;
            $tipos .= "s";
        }

        $stmt = self::$db->prepare($query);
        if ($tipos) {
            $stmt->bind_param($tipos, ...$valores);
        }
        $stmt->execute();
        $resultado = $stmt->get_result();

        $final = [];
        while ($registro = $resultado->fetch_assoc()) {
            $final[] = static::crearObjeto($registro);
        }
        $stmt->close();
        return $final;
    }

    /**
     * Cuenta el total de registros, opcionalmente filtrados por una columna
     * 
     * @param string $columna Nombre de la columna para filtrar (opcional)
     * @param mixed $valor Valor para filtrar (opcional)
     * @return int El conteo total de registros
     */
    public static function total(string $columna = '', mixed $valor = ''): int
    {
        $query = "SELECT COUNT(*) FROM " . static::$tabla;
        if ($columna) {
            $query .= " WHERE {$columna} = ?";
            $stmt = self::$db->prepare($query);
            $stmt->bind_param("s", $valor);
            $stmt->execute();
            $resultado = $stmt->get_result();
        } else {
            $resultado = self::$db->query($query);
        }

        $total = $resultado->fetch_array();
        if (isset($stmt)) $stmt->close();

        return (int) array_shift($total);
    }

    /**
     * Cuenta el total de registros que cumplen con múltiples criterios
     * 
     * @param array $array Mapa de columna => valor para filtrar
     * @return int El conteo total de registros
     */
    public static function totalArray(array $array = []): int
    {
        $query = "SELECT COUNT(*) FROM " . static::$tabla . " WHERE ";
        $valores = [];
        $tipos = "";

        foreach ($array as $key => $value) {
            $query .= " {$key} = ? ";
            if ($key !== array_key_last($array)) {
                $query .= " AND ";
            }
            $valores[] = $value;
            $tipos .= "s";
        }

        $stmt = self::$db->prepare($query);
        $stmt->bind_param($tipos, ...$valores);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $total = $resultado->fetch_array();
        $stmt->close();
        return (int) array_shift($total);
    }

    /**
     * Inserta un nuevo registro en la base de datos usando sentencias preparadas
     * 
     * @return array Información sobre el resultado y el nuevo ID
     */
    public function crear(): array
    {
        $atributos = $this->atributos();

        $query = " INSERT INTO " . static::$tabla . " ( ";
        $query .= join(', ', array_keys($atributos));
        $query .= " ) VALUES ( ";
        $query .= str_repeat('?, ', count($atributos) - 1) . "?";
        $query .= " ) ";

        $stmt = self::$db->prepare($query);
        $tipos = str_repeat('s', count($atributos));
        $valores = array_values($atributos);

        $stmt->bind_param($tipos, ...$valores);
        $resultado = $stmt->execute();
        $id = self::$db->insert_id;
        $stmt->close();

        return [
            'resultado' => $resultado,
            'id' => $id
        ];
    }

    /**
     * Actualiza el registro actual en la base de datos usando sentencias preparadas
     * 
     * @return bool Resultado de la actualización
     */
    public function actualizar(): bool
    {
        $atributos = $this->atributos();

        $valores = [];
        foreach ($atributos as $key => $value) {
            $valores[] = "{$key}=?";
        }

        $query = "UPDATE " . static::$tabla . " SET ";
        $query .=  join(', ', $valores);
        $query .= " WHERE id = ? LIMIT 1 ";

        $stmt = self::$db->prepare($query);
        $tipos = str_repeat('s', count($atributos)) . "s";

        $final_values = array_values($atributos);
        $final_values[] = $this->id;

        $stmt->bind_param($tipos, ...$final_values);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }

    /**
     * Elimina el registro actual de la base de datos usando sentencias preparadas
     * 
     * @return bool Resultado de la eliminación
     */
    public function eliminar(): bool
    {
        $query = "DELETE FROM "  . static::$tabla . " WHERE id = ? LIMIT 1";
        $stmt = self::$db->prepare($query);
        $stmt->bind_param("s", $this->id);
        $resultado = $stmt->execute();
        $stmt->close();

        return $resultado;
    }
}
