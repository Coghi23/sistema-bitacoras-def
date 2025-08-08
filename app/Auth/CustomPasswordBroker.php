<?php

namespace App\Auth;

use Illuminate\Auth\Passwords\PasswordBroker as BasePasswordBroker;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\Hash;

class CustomPasswordBroker extends BasePasswordBroker
{
    /**
     * Send a password reset link to a user.
     */
    public function sendResetLink(array $credentials, \Closure $callback = null)
    {
        // Obtener el usuario
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        if ($this->tokens->recentlyCreatedToken($user)) {
            return static::RESET_THROTTLED;
        }

        // Crear token plano
        $plainToken = $this->tokens->createToken($user);
        
        // Hashear el token antes de guardarlo
        $hashedToken = Hash::make($plainToken);
        
        // Actualizar en la base de datos con el token hasheado
        \DB::table('password_reset_tokens')
            ->where('email', $user->getEmailForPasswordReset())
            ->update(['token' => $hashedToken]);

        if ($callback) {
            $callback($user, $plainToken); // Enviar el token plano al email
        }

        return static::RESET_LINK_SENT;
    }
}
