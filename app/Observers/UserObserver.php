<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Profesor;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Verificar si el usuario tiene el rol de profesor al ser creado
        if ($user->hasRole('profesor') && !$user->profesor) {
            $this->createProfesorProfile($user);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        // Verificar si el usuario tiene el rol de profesor y no tiene perfil
        if ($user->hasRole('profesor') && !$user->profesor) {
            $this->createProfesorProfile($user);
        }
        
        // Si el usuario NO tiene rol profesor pero SÍ tiene perfil, eliminar el perfil
        if (!$user->hasRole('profesor') && $user->profesor) {
            $user->profesor->delete();
            \Log::info("Perfil de profesor eliminado para usuario: {$user->name}");
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        // Si se elimina el usuario, también eliminar el perfil de profesor
        if ($user->profesor) {
            $user->profesor->delete();
        }
    }

    /**
     * Crear perfil de profesor
     */
    private function createProfesorProfile(User $user): void
    {
        Profesor::create([
            'usuario_id' => $user->id
        ]);
        
        \Log::info("Perfil de profesor creado automáticamente para usuario: {$user->name}");
    }
}
