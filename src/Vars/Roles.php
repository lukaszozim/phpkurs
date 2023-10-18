<?php

namespace App\Vars;

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

}