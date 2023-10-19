<?php

namespace App\Vars;

use App\DTO\UserDTO;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;

class Roles {

    const AVAILABLE_ROLES = ['read', 'vip', 'adm'];


    public static function setRoleOnRequest(Request $request) {

        if (in_array($request->headers->get('auth'), Self::AVAILABLE_ROLES)) {

            $role = $request->headers->get('auth');
        } else {

            $role = 'read';
        }

        return $role;
    }

    public static function analyzeEmail(UserDTO|User $userDto): bool
    {
        $domain = '';

        if ($userDto instanceof UserDTO) {
            $domain = explode('@', $userDto->email);
        } elseif ($userDto instanceof User) {
            $domain = explode('@', $userDto->getEmail());
        }

        if ($domain[1] === 'gmail.com') {

            return true;
        } else {

            return false;
        }
    }

    public static function analyzePhoneNumber(UserDTO|User $userDto): bool
    {
        $stringNumber= '';
        if($userDto instanceof UserDTO) {
            $stringNumber = strval($userDto->phoneNumber);
        } elseif ($userDto instanceof User) {
            $stringNumber = strval($userDto->getPhoneNumber());
        }


        $stringNumber = strval($userDto->phoneNumber);
        //if the number begins at 666 it is special role;
        if(intval(preg_match('/^666\d+$/', $stringNumber)))
        {

            return true;
        } else {

            return false;
        }
    }

}