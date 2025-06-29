<?php

namespace App\Exception;


use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\RuntimeException;

#[WithHttpStatus(403)]
class CustomApiAccessDeniedException extends HttpException
{

    public function __construct()
    {
        parent::__construct(statusCode:403, message:'Accès API non activé. Activez-le dans votre profil.');
    }



}
