<?php

namespace App\Auth;

use Illuminate\Auth\Passwords\DatabaseTokenRepository as BaseDatabaseTokenRepository;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Facades\Hash;

class CustomDatabaseTokenRepository extends BaseDatabaseTokenRepository
{
    /**
     * Determine if a token record exists and is valid.
     */
    public function exists(CanResetPasswordContract $user, $token)
    {
        $record = (array) $this->getTable()->where(
            'email', $user->getEmailForPasswordReset()
        )->first();

        return $record &&
               ! $this->tokenExpired($record['created_at']) &&
               Hash::check($token, $record['token']); // Usar Hash::check para comparar
    }

    /**
     * Delete a token record by user.
     */
    public function delete(CanResetPasswordContract $user)
    {
        $this->getTable()->where('email', $user->getEmailForPasswordReset())->delete();
    }

    /**
     * Delete expired tokens.
     */
    public function deleteExpired()
    {
        $expiredAt = $this->getTable()->getConnection()->raw(
            'DATE_SUB(NOW(), INTERVAL '.$this->expires.' MINUTE)'
        );

        $this->getTable()->where('created_at', '<', $expiredAt)->delete();
    }
}
