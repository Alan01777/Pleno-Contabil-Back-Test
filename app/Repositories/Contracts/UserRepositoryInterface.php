<?php

namespace App\Repositories\Contracts;

interface UserRepositoryInterface
{
    /**
     * Create a new user.
     *
     * @param array $data The user data.
     * @return mixed The created user.
     */
    public function create(array $data): mixed;

    /**
     * Find a user by ID.
     *
     * @param int $userId The user ID.
     * @return mixed The found user.
     */
    public function getById(int $userId): mixed ;

    /**
     * Update a user.
     *
     * @param int $userId The user ID.
     * @param array $data The updated user data.
     * @return mixed The updated user.
     */
    public function update(int $userId, array $data): mixed;

    /**
     * Delete a user.
     *
     * @param int $userId The user ID.
     * @return null return null if the user was deleted successfully
     */
    public function delete(int $userId);


     /**
     * Find a user by their email address.
     *
     * @param string $email
     * @return User|null
     */
    public function findByEmail(string $email);
}