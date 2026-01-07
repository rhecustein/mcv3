<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class PasswordHelper
{
    /**
     * Generate a secure random password.
     *
     * @param int $length Length of the password
     * @return string
     */
    public static function generateSecure(int $length = 16): string
    {
        // Generate a strong random password with mixed characters
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz23456789!@#$%^&*';
        $password = '';
        $max = strlen($characters) - 1;

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, $max)];
        }

        return $password;
    }

    /**
     * Generate password data for a new user account.
     * Returns both the hashed password and plain password for notification.
     *
     * @return array{password: string, plain_password: string, must_change_password: bool}
     */
    public static function generateForNewUser(): array
    {
        $plainPassword = self::generateSecure(12);

        return [
            'password' => bcrypt($plainPassword),
            'plain_password' => $plainPassword,
            'must_change_password' => true,
        ];
    }

    /**
     * Generate a temporary password for password reset.
     *
     * @return array{password: string, plain_password: string}
     */
    public static function generateTemporary(): array
    {
        $plainPassword = self::generateSecure(8);

        return [
            'password' => bcrypt($plainPassword),
            'plain_password' => $plainPassword,
        ];
    }

    /**
     * Validate password strength.
     * Password must be at least 8 characters, contain uppercase, lowercase, and number.
     *
     * @param string $password
     * @return bool
     */
    public static function validateStrength(string $password): bool
    {
        return strlen($password) >= 8 &&
               preg_match('/[A-Z]/', $password) &&
               preg_match('/[a-z]/', $password) &&
               preg_match('/[0-9]/', $password);
    }

    /**
     * Get password strength requirements message.
     *
     * @return string
     */
    public static function getRequirementsMessage(): string
    {
        return 'Password harus minimal 8 karakter dan mengandung huruf besar, huruf kecil, dan angka.';
    }
}
