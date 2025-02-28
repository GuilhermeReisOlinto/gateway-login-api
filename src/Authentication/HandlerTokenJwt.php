<?php

namespace App\Authentication;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class HandlerTokenJwt 
{
    private string $key = "j08aewijt989028h3truioqw49g8v";

    public function generateToken($payload)
    {
        $jwt = JWT::encode($payload, $this->key, 'HS256');
        
        return $jwt;
    }

    public function decodificateToken($jwt)
    {
        $decode = JWT::decode($jwt, new Key($this->key, 'HS256'));

        return $decode;
    }
}