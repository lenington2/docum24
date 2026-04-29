<?php

namespace App\Services;

use App\Models\Suscripcion;
use App\Models\TokenLog;
use App\Models\Plane;
use App\Models\User;

class TokenService
{
    // Obtener suscripción activa del usuario
    public function getSuscripcionActiva(User $user): ?Suscripcion
    {
        return Suscripcion::where('user_id', $user->id)
            ->where('estado', 'activa')
            ->where('fecha_fin', '>=', now()->toDateString())
            ->first();
    }

    // Registrar consumo de tokens
    public function registrar(User $user, string $tipo, int $input, int $output, string $modelo): void
    {
        $suscripcion = $this->getSuscripcionActiva($user);
        if (!$suscripcion) return;

        $total = $input + $output;

        TokenLog::create([
            'user_id'         => $user->id,
            'suscripcion_id'  => $suscripcion->id,
            'tipo'            => $tipo,
            'tokens_input'    => $input,
            'tokens_output'   => $output,
            'tokens_total'    => $total,
            'modelo'          => $modelo,
            'created_at'      => now(),
        ]);

        $suscripcion->increment('tokens_usados', $total);
    }

    // Verificar si puede usar funciones avanzadas (chat documentos)
    public function puedeUsarAvanzato(User $user): bool
    {
        $sus = $this->getSuscripcionActiva($user);
        if (!$sus) return false;
        return !$sus->isAgotada();
    }

    
    // Crear suscripción Trial al registro
    public function crearTrialParaUsuario(User $user): void
    {
        $planTrial = Plane::where('nombre', 'Trial')->first();
        if (!$planTrial) return;

        Suscripcion::create([
            'user_id'        => $user->id,
            'plan_id'        => $planTrial->id,
            'tokens_usados'  => 0,
            'tokens_limite'  => $planTrial->tokens_mes,
            'fecha_inicio'   => now()->toDateString(),
            'fecha_fin'      => now()->addDays($planTrial->duracion_dias)->toDateString(),
            'estado'         => 'activa',
        ]);
    }

    // Obtener plan activo del usuario
    public function getPlanActivo(User $user): ?\App\Models\Plane
    {
        $sus = $this->getSuscripcionActiva($user);
        return $sus?->plan;
    }

    // Verificar límite de proyectos
    public function puedeCrearProyecto(User $user): bool
    {
        $plan = $this->getPlanActivo($user);
        if (!$plan) return false;
        if ($plan->max_proyectos === -1) return true;

        $count = \App\Models\Proyecto::where('user_id', $user->id)->count();
        return $count < $plan->max_proyectos;
    }

    // Verificar límite de categorías por proyecto
    public function puedeCrearCategoria(User $user, int $proyectoId): bool
    {
        $plan = $this->getPlanActivo($user);
        if (!$plan) return false;
        if ($plan->max_categorias === -1) return true;

        $count = \App\Models\Categoria::where('proyecto_id', $proyectoId)->count();
        return $count < $plan->max_categorias;
    }

    // Verificar límite de tipologías por categoría
    public function puedeCrearTipologia(User $user, int $categoriaId): bool
    {
        $plan = $this->getPlanActivo($user);
        if (!$plan) return false;
        if ($plan->max_tipologias === -1) return true;

        $count = \App\Models\Tipologia::where('categoria_id', $categoriaId)->count();
        return $count < $plan->max_tipologias;
    }

    // Verificar límite de storage
    public function puedeSubirArchivo(User $user, int $fileSizeBytes): bool
    {
        $plan = $this->getPlanActivo($user);
        if (!$plan) return false;

        $maxBytes   = $plan->max_storage_mb * 1024 * 1024;
        $usedBytes  = \App\Models\Documento::where('user_id', $user->id)
            ->join('files_storage', 'documentos.archivo', '=', 'files_storage.path')
            ->sum('files_storage.size') ?? 0;

        // Calcular storage usado directamente desde disco
        $usedBytes = $this->calcularStorageUsado($user);

        return ($usedBytes + $fileSizeBytes) <= $maxBytes;
    }

    // Calcular storage usado en disco
    public function calcularStorageUsado(User $user): int
    {
        $documentos = \App\Models\Documento::where('user_id', $user->id)->get();
        $total = 0;
        foreach ($documentos as $doc) {
            try {
                if (\Illuminate\Support\Facades\Storage::disk('local')->exists($doc->archivo)) {
                    $total += \Illuminate\Support\Facades\Storage::disk('local')->size($doc->archivo);
                }
            } catch (\Exception $e) {
            }
        }
        return $total;
    }

    // Datos completos para el header incluyendo storage
    public function getHeaderData(User $user): array
    {
        $sus = $this->getSuscripcionActiva($user);
        if (!$sus) {
            return [
                'tiene_suscripcion' => false,
                'porcentaje'        => 0,
                'tokens_usados'     => 0,
                'tokens_limite'     => 0,
                'tokens_restantes'  => 0,
                'plan'              => 'Nessun piano',
                'agotada'           => true,
                'storage_usado_mb'  => 0,
                'storage_limite_mb' => 0,
                'storage_porcentaje' => 0,
            ];
        }

        $plan          = $sus->plan;
        $storageUsado  = $this->calcularStorageUsado($user);
        $storageLimite = $plan->max_storage_mb * 1024 * 1024;
        $storagePct    = $storageLimite > 0
            ? round(($storageUsado / $storageLimite) * 100, 1)
            : 0;

        return [
            'tiene_suscripcion'  => true,
            'porcentaje'         => $sus->porcentaje_usado,
            'tokens_usados'      => $sus->tokens_usados,
            'tokens_limite'      => $sus->tokens_limite,
            'tokens_restantes'   => $sus->tokens_restantes,
            'plan'               => $plan->nombre,
            'agotada'            => $sus->isAgotada(),
            'fecha_fin'          => $sus->fecha_fin->format('d/m/Y'),
            'storage_usado_mb'   => round($storageUsado / 1024 / 1024, 1),
            'storage_limite_mb'  => $plan->max_storage_mb,
            'storage_porcentaje' => $storagePct,
            // Límites plan
            'max_proyectos'      => $plan->max_proyectos,
            'max_categorias'     => $plan->max_categorias,
            'max_tipologias'     => $plan->max_tipologias,
        ];
    }
}
