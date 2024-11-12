<?php

namespace App\Repositories\Resources;

use App\Http\Exceptions\NullValueException;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;


class UserRepository implements UserRepositoryInterface
{
    private User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Create a new user.
     *
     * @param array $data The data for creating the user.
     * @return User The created user.
     */
    public function create(array $data): User
    {
        $user = new $this->user([
            'cnpj' => $data['cnpj'],
            'razao_social' => $data['razao_social'],
            'nome_fantasia' => $data['nome_fantasia'],
            'porte' => $data['porte'],
            'endereco' => $data['endereco'],
            'telefone' => $data['telefone'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user->save();

        return $user;
    }

    /**
     * Find a user by ID.
     *
     * @param int $userId The ID of the user to find.
     * @return User The found user.
     * @throws NullValueException If no user is found with the given ID.
     */
    public function getById(int $userId): User
    {
        $user = $this->user->where('id', $userId)->first();
        if (!$user) {
            throw new NullValueException('No user found with id' . $userId);
        }
        return $user;
    }

    /**
     * Update a user by ID.
     *
     * @param int $userId The ID of the user to update.
     * @param array $data The data for updating the user.
     * @return User The updated user.
     * @throws NullValueException If no user is found with the given ID.
     */
    public function update(int $userId, array $data): User
    {
        $user = $this->getById($userId);
        if (!$user) {
            throw new NullValueException('No user found with id: ' . $userId);
        }
        $user->update($data);
        return $user;
    }

    /**
     * Delete a user by ID.
     *
     * @param int $userId The ID of the user to delete.
     * @throws NullValueException If no user is found with the given ID.
     */
    public function delete(int $userId)
    {
        $user = $this->getById($userId);
        if (!$user) {
            throw new NullValueException('No user found with id' . $userId);
        }
        $user->delete();
    }

    /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    /**
     * Get the reset token for a given token string.
     *
     * @param string $token
     * @return PasswordReset|null
     */
    public function getResetToken(string $token): ?PasswordResetToken
    {
        $correctToken = PasswordResetToken::with('user')->first();

        if (Hash::check($token, $correctToken->token)) {
            return $correctToken;
        } else {
            return null;
        }
    }

    /**
     * Update the password for a given user.
     *
     * @param User $user
     * @param string $password
     * @return void
     */
    public function updatePassword(User $user, string $password): void
    {
        $user->password = Hash::make($password);
        $user->save();
    }

    /**
     * Delete the reset token for a given email.
     *
     * @param string $email
     * @return void
     */
    public function deleteResetToken(string $email): void
    {
        DB::table('password_reset_tokens')->where('email', $email)->delete();
    }
}
