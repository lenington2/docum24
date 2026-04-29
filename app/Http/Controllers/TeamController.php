<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\TeamInvitation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TeamController extends Controller
{
    // ── LISTAR TEAMS DEL USUARIO ──────────────────────────────
    public function index()
    {
        $user = Auth::user();
        $ownedTeams = Team::where('user_id', $user->id)
            ->with(['users', 'proyectos'])
            ->get();
        $memberTeams = $user->teams()->with(['owner', 'proyectos'])->get();

        return response()->json([
            'owned'  => $ownedTeams,
            'member' => $memberTeams,
        ]);
    }

    // ── CREAR TEAM ────────────────────────────────────────────
    public function store(Request $request)
    {
        $user = Auth::user();

        // Verificar límite: max 3 teams para plan Pro
        $count = Team::where('user_id', $user->id)->count();
        if ($count >= 3) {
            return response()->json([
                'success' => false,
                'error'   => 'limite_raggiunto',
                'message' => 'Hai raggiunto il limite di 3 team per il piano Pro.',
            ], 403);
        }

        $request->validate([
            'name' => 'required|string|max:100',
        ]);

        $team = Team::create([
            'user_id' => $user->id,
            'name'    => $request->name,
        ]);

        // Actualizar current_team si no tiene
        if (!$user->current_team_id) {
            $user->update(['current_team_id' => $team->id]);
        }

        return response()->json(['success' => true, 'team' => $team]);
    }

    // ── ACTUALIZAR TEAM ───────────────────────────────────────
    public function update(Request $request, Team $team)
    {
        if ($team->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $request->validate(['name' => 'required|string|max:100']);
        $team->update(['name' => $request->name]);

        return response()->json(['success' => true, 'team' => $team]);
    }

    // ── ELIMINAR TEAM ─────────────────────────────────────────
    public function destroy(Team $team)
    {
        if ($team->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $team->delete();
        return response()->json(['success' => true]);
    }

    // ── INVITAR MIEMBRO ───────────────────────────────────────
    public function invite(Request $request, Team $team)
    {
        $user = Auth::user();

        if ($team->user_id !== $user->id) {
            return response()->json(['success' => false], 403);
        }

        // Verificar límite: max 5 miembros totales en todos los teams
        $totalMembers = Team::where('user_id', $user->id)
            ->withCount('users')
            ->get()
            ->sum('users_count');

        if ($totalMembers >= 5) {
            return response()->json([
                'success' => false,
                'error'   => 'limite_raggiunto',
                'message' => 'Hai raggiunto il limite di 5 membri per il piano Pro.',
            ], 403);
        }

        $request->validate([
            'email' => 'required|email',
            'role'  => 'required|in:editor,viewer',
        ]);

        // Verificar si ya está invitado
        $existing = TeamInvitation::where('team_id', $team->id)
            ->where('email', $request->email)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Invito già inviato a questa email.',
            ], 422);
        }

        // Verificar si ya es miembro
        $existingUser = User::where('email', $request->email)->first();
        if ($existingUser && $team->hasUser($existingUser)) {
            return response()->json([
                'success' => false,
                'message' => 'Questo utente è già membro del team.',
            ], 422);
        }

        $invitation = TeamInvitation::create([
            'team_id' => $team->id,
            'email'   => $request->email,
            'role'    => $request->role,
        ]);

        // Enviar email de invitación
        $this->sendInvitationEmail($invitation, $team, $user);

        return response()->json(['success' => true, 'invitation' => $invitation]);
    }

    // ── ACEPTAR INVITACIÓN ────────────────────────────────────
    public function acceptInvitation(Request $request, TeamInvitation $invitation)
    {
        $user = Auth::user();

        if ($user->email !== $invitation->email) {
            return response()->json(['success' => false], 403);
        }

        // Añadir al team
        $invitation->team->users()->attach($user->id, ['role' => $invitation->role]);

        // Actualizar current_team
        $user->update(['current_team_id' => $invitation->team_id]);

        // Eliminar invitación
        $invitation->delete();

        return response()->json(['success' => true, 'team' => $invitation->team]);
    }

    // ── ELIMINAR MIEMBRO ──────────────────────────────────────
    public function removeMember(Request $request, Team $team, User $member)
    {
        if ($team->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $team->users()->detach($member->id);
        return response()->json(['success' => true]);
    }

    // ── CAMBIAR ROL ───────────────────────────────────────────
    public function updateRole(Request $request, Team $team, User $member)
    {
        if ($team->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $request->validate(['role' => 'required|in:editor,viewer']);
        $team->users()->updateExistingPivot($member->id, ['role' => $request->role]);

        return response()->json(['success' => true]);
    }

    // ── ASIGNAR PROYECTO A TEAM ───────────────────────────────
    public function assignProject(Request $request, Team $team)
    {
        if ($team->user_id !== Auth::id()) {
            return response()->json(['success' => false], 403);
        }

        $request->validate(['proyecto_id' => 'required|exists:proyectos,id']);

        \App\Models\Proyecto::where('id', $request->proyecto_id)
            ->where('user_id', Auth::id())
            ->update(['team_id' => $team->id]);

        return response()->json(['success' => true]);
    }

    // ── CAMBIAR TEAM ACTIVO ───────────────────────────────────
    public function switchTeam(Request $request, Team $team)
    {
        $user = Auth::user();

        if (!$team->hasUser($user)) {
            return response()->json(['success' => false], 403);
        }

        $user->update(['current_team_id' => $team->id]);
        return response()->json(['success' => true]);
    }

    // ── EMAIL INVITACIÓN ──────────────────────────────────────
    private function sendInvitationEmail(TeamInvitation $invitation, Team $team, User $owner)
    {
        try {
            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($invitation, $team, $owner) {
                $acceptUrl = url('/teams/invitations/' . $invitation->id . '/accept');
                $message
                    ->to($invitation->email)
                    ->subject("Sei invitato nel team {$team->name} — Docum24")
                    ->html("
                        <div style='font-family:sans-serif;max-width:520px;margin:0 auto;padding:32px;'>
                            <h2 style='color:#1b1b18;'>Invito al team</h2>
                            <p style='color:#706f6c;'><strong>{$owner->name}</strong> ti ha invitato a far parte del team <strong>{$team->name}</strong> su Docum24 come <strong>{$invitation->role}</strong>.</p>
                            <a href='{$acceptUrl}'
                               style='display:inline-block;margin-top:20px;background:#1b1b18;color:#fff;
                                      padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;'>
                                Accetta invito
                            </a>
                            <p style='margin-top:20px;font-size:12px;color:#a8a7a3;'>Il team di Docum24</p>
                        </div>
                    ");
            });
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Team invitation email error', ['error' => $e->getMessage()]);
        }
    }
}
