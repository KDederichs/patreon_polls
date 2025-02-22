<?php

namespace App\Security;

use App\Entity\User;

interface UserOwnedInterface
{
    public function getUser(): User;
    public static function getUserField(): string;
}
