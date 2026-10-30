<?php

namespace App\Chat;

use App\Chat\Tools;

/**
 * Registro central de todos los ChatTools disponibles.
 * Cada tool se instancia una vez y se cachea.
 */
class ToolRegistry
{
    private static ?array $tools = null;

    /**
     * Lista de clases de tools registrados.
     * Para agregar un tool nuevo, solo añadir aquí.
     */
    private static array $toolClasses = [
        Tools\CreateProject::class,
        Tools\CreateCategory::class,
        Tools\CreateTypology::class,
        Tools\SearchFiles::class,
        Tools\OpenProject::class,
        Tools\OpenFile::class,
        Tools\ListProjects::class,
        Tools\ManageEntities::class,
        Tools\GenerateReport::class,
        Tools\ToggleSetting::class,
    ];

    /**
     * Obtener todos los tools instanciados, indexados por nombre.
     */
    public static function all(): array
    {
        if (self::$tools === null) {
            self::$tools = [];
            foreach (self::$toolClasses as $class) {
                $tool = new $class();
                $def = $tool->definition();
                self::$tools[$def['name']] = $tool;
            }
        }
        return self::$tools;
    }

    /**
     * Obtener las definiciones de todos los tools (para enviar a Claude).
     */
    public static function definitions(): array
    {
        $defs = [];
        foreach (self::all() as $tool) {
            $defs[] = $tool->definition();
        }
        return $defs;
    }

    /**
     * Obtener un tool por nombre.
     */
    public static function get(string $name): ?ChatTool
    {
        return self::all()[$name] ?? null;
    }

    /**
     * Resetear caché (para testing).
     */
    public static function reset(): void
    {
        self::$tools = null;
    }
}
