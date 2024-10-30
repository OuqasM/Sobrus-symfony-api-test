<?php

namespace App\Handler;

use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ChangePasswordHandler
{

    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function changePassword($data)
    {
        $hashedPassword = $this->passwordHasher->hashPassword($data, $data->getPassword());
        $data->setPassword($hashedPassword);

        return $data;
    }
}