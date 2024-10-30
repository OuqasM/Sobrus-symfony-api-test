<?php

namespace App\Controller\User;

use App\Handler\ChangePasswordHandler;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ChangePasswordController extends AbstractController
{

    public function __construct(public ChangePasswordHandler $handler)
    {

    }

    public function __invoke($data)
    {
        return $this->handler->changePassword($data);
    }
}