<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class UsernameNotFoundException extends CustomUserMessageAuthenticationException
{
    public function __construct($message = 'Participant not found.', array $messageData = [], $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $messageData, $code, $previous);
    }
}