<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AuthorizationController extends AbstractController{

    public function userAuthorization($apiKey)
    {
        if ($apiKey != $this->getParameter('api_key')) {
            exit('Authorization has failed');
        }

        return;
    }
}