<?php

namespace App\Exception;

use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;

class UserNotActiveException extends CustomUserMessageAuthenticationException
{
    public function __construct($message = 'User account is not active.', array $messageData = [], $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $messageData, $code, $previous);
    }
}
