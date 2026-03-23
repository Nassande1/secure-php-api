<?php

namespace Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\ExpiredException;

class Auth {
    private static $secretKey = "your_secret_key_here";
    private static $algorithm = 'HS256'; // Algorithm used to sign the token

    public static function jwt($request) {
        $headers = $request->getHeaders();
        if (isset($headers['Authorization'])) {
            $authHeader = $headers['Authorization'];
            list($jwt) = sscanf($authHeader, 'Authorization: Bearer %s');
            return self::validateJWT($jwt);
        }
        return null;
    }

    private static function validateJWT($jwt) {
        try {
            return JWT::decode($jwt, self::getSecretKey(), [self::$algorithm]);
        } catch (ExpiredException $e) {
            throw new Exception('Token expired', 401);
        } catch (Exception $e) {
            throw new Exception('Authorization Token not recognized', 401);
        }
    }

    private static function getSecretKey() {
        return self::$secretKey;
    }
}