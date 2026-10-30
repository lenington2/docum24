<?php

namespace App\Chat;

/**
 * Interface para todos los tools del chat.
 *
 * Cada tool define su schema (para Claude tool_use)
 * y su lógica de ejecución.
 */
interface ChatTool
{
    /**
     * Definición del tool para la API de Claude.
     * Retorna array con: name, description, input_schema
     */
    public function definition(): array;

    /**
     * Ejecuta el tool con los parámetros que Claude envió.
     *
     * @param array $input  Parámetros del tool_use
     * @param mixed $user   Usuario autenticado
     * @return array        Resultado con:
     *   - tool_result: string para devolver a Claude (resumen de lo que pasó)
     *   - frontend_action: string (acción para el frontend, nullable)
     *   - frontend_payload: array (datos para el frontend, nullable)
     */
    public function execute(array $input, $user): array;
}
