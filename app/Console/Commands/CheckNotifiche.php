<?php

namespace App\Console\Commands;

use App\Models\Notifica;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Carbon;

class CheckNotifiche extends Command
{
    protected $signature   = 'notifiche:check';
    protected $description = 'Controlla e invia notifiche di scadenza';

    public function handle(): void
    {
        $oggi = Carbon::today();

        $notifiche = Notifica::where('estado', 'pendiente')
            ->with(['documento', 'user'])
            ->get()
            ->filter(function ($n) use ($oggi) {
    $giornoInvio = Carbon::parse($n->fecha_scadenza)->subDays($n->dias_antes);
    return $oggi->greaterThanOrEqualTo($giornoInvio)
        && Carbon::parse($n->fecha_scadenza)->greaterThanOrEqualTo($oggi);
});

        foreach ($notifiche as $notifica) {
            try {
                Mail::send([], [], function ($message) use ($notifica) {
                    $doc        = $notifica->documento;
                    $scadenza   = Carbon::parse($notifica->fecha_scadenza)->format('d/m/Y');
                    $giorni     = Carbon::today()->diffInDays(Carbon::parse($notifica->fecha_scadenza));

                    $message
                        ->to($notifica->email)
                        ->subject("⏰ Scadenza documento — {$doc->nombre}")
                        ->html("
                            <div style='font-family:sans-serif;max-width:520px;margin:0 auto;padding:32px;'>
                                <h2 style='color:#1b1b18;margin-bottom:8px;'>Promemoria scadenza</h2>
                                <p style='color:#706f6c;'>Il seguente documento sta per scadere:</p>
                                <div style='background:#f9f9f8;border:1px solid #e3e3e0;border-radius:10px;padding:16px 20px;margin:20px 0;'>
                                    <p style='margin:0;font-weight:600;color:#1b1b18;'>{$doc->nombre}</p>
                                    <p style='margin:4px 0 0;font-size:13px;color:#706f6c;'>Scadenza: <strong>{$scadenza}</strong> — tra {$giorni} giorni</p>
                                </div>
                                <p style='color:#706f6c;font-size:13px;'>Accedi a Docum24 per gestire il documento.</p>
                                <p style='color:#a8a7a3;font-size:11px;margin-top:32px;'>Il team di Docum24</p>
                            </div>
                        ");
                });

                $notifica->update([
                    'estado'     => 'enviada',
                    'enviada_at' => now(),
                ]);

                $this->info("Inviata: {$notifica->documento->nombre}");

            } catch (\Exception $e) {
                $this->error("Errore: {$e->getMessage()}");
            }
        }

        $this->info("Completato. {$notifiche->count()} notifiche processate.");
    }
}
