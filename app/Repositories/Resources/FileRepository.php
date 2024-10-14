<?php

namespace App\Repositories\Resources;

use App\Models\File;
use App\Models\User;

class FileRepository
{
    protected $file, $user;

    public function __construct(File $file, User $user)
    {
        $this->file = $file;
        $this->user = $user;

    }

    public function uploadFile($data)
    {
        $user = $this->user->find($data['user_id']);

        if(!$user){
            throw new \Exception('No user found');
        }

        // Create a new file
        return $this->file->create([
            'name' => $data['name'],
            'user_id' => $user->id,
            'path' => $data['path'] . '/' . $data['name']
        ]);
    }
}