<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/DatabaseConfig.php';
require_once __DIR__ . '/../DAO/UserDao.php';

class ConfigJWT{
    private static function base64UrlEncode(string $data): string{
        return rtrim(
            strtr(base64_encode($data), '+/', '-_'),
            '='
        );
    }

    private static function base64UrlDecode(string $data): string|false{
        $data = strtr($data, '-_', '+/');

        $padding = strlen($data) % 4;

        if ($padding !== 0) {
            $data .= str_repeat('=', 4 - $padding);
        }

        return base64_decode($data, true);
    }


    private static function validateToken(string $token, string $type = 'access'): bool{

        $parts =  explode('.', $token);
        if(count($parts) !== 3){
            return false;
        }
        [$header64, $payload64, $signature64] = $parts;

        $headerJson = self::base64UrlDecode($header64);
        $payloadJson = self::base64UrlDecode($payload64);
        $signatureJson = self::base64UrlDecode($signature64);

        if($headerJson === false || $payloadJson === false || $signatureJson === false){
            return false;
        }

        try {
            $payload = json_decode($payloadJson, true, 512, JSON_THROW_ON_ERROR);
            $header = json_decode($headerJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return false;
        }

        //validate header
        if(!isset($header) ||
            !is_array($header) ||
            !isset($header['typ']) ||
            !isset($header['alg']) ||
            $header['typ'] !== 'JWT' ||
            $header['alg'] !== 'HS256'
        ){
            return false;
        }

        //validate payload
        if(
            !isset($payload) ||
            !isset($payload['id']) || 
            !isset($payload['type']) ||
            !isset($payload['iat']) || 
            !isset($payload['exp'])
        ){
            return false;
        }

        if(
            !is_array($payload) || 
            !is_int($payload['id'])||
            !is_string($payload['type']) ||
            !is_int($payload['iat']) ||
            !is_int($payload['exp'])
        ){
            return false;
        }

        $qtdUsers = UserDao::countUser(DatabaseConfig::conectar());
        $now = time();

        if(
            $payload['id'] > $qtdUsers || 
            $payload['id'] <= 0 || 
            $payload['type'] !== $type || 
            $payload['iat'] > $now ||
            $payload['exp'] <= $now
        ){
            return false;
        }

        //validate signature
        $dados = $header64 . '.' . $payload64;

        $signatureEsperada = hash_hmac(
            'sha256',
            $dados,
            PASSWORD_JWT,
            true
        );

        if (!hash_equals($signatureEsperada, $signatureJson)) {
            return false;
        }


        return true;
    }

    public static function generateToken(int $userid): string|false{
        $now = time();
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ], JSON_THROW_ON_ERROR);

        $payload = json_encode([
            'id' => $userid,
            'type' => 'access',
            'iat' => $now,
            'exp' => $now + 1800
        ], JSON_THROW_ON_ERROR);

        $header64 =  self::base64UrlEncode($header);
        $payload64 = self::base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $header64. '.' . $payload64, PASSWORD_JWT, true);
        $signature64 = self::base64UrlEncode($signature);

        $token = $header64 . '.' . $payload64 . '.' . $signature64;

        return $token;

    }

    public static function validateRefreshToken(string $token): bool{
        if(!self::validateToken($token)){
            if(!self::validateToken($token, 'refresh')){
                return false;
            }
        }

        $now = time();

        $parts =  explode('.', $token);
        if(count($parts) !== 3){
            return false;
        }
        [$header64, $payload64, $signature64] = $parts;

        $headerJson = self::base64UrlDecode($header64);
        $payloadJson = self::base64UrlDecode($payload64);
        $signatureJson = self::base64UrlDecode($signature64);

        if($headerJson === false || $payloadJson === false || $signatureJson === false){
            return false;
        }
        try {
            $payload = json_decode($payloadJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return false;
        }
        
        if($now > $payload['exp']){
            return false;
        }

        return true;
    }

    public static function getUserId(string $token): int|false{

        if(!self::validateToken($token)){
            if(!self::validateToken($token, 'refresh')){
                return false;
            }
        }
        $parts = explode('.', $token);

        if (count($parts) !== 3 || !isset($parts)) {
            return false;
        }

        [$header64, $payload64, $signature64] = $parts;

        $payloadJson =  self::base64UrlDecode($payload64);
        if($payloadJson === false){
            return false;
        }
        try {
            $payload = json_decode($payloadJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            return false;
        }

        if(!is_array($payload) || !isset($payload['id']) || !is_int($payload['id'])){
            return false;
        }
        
        return $payload['id'];
    }

    public static function refreshToken(string $token): string|false{
        
        $id = self::getUserId($token);
        if(!$id){
            return false;
        }

        $now = time();
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ], JSON_THROW_ON_ERROR);

        $payload = json_encode([
            'id' => $id,
            'type' => 'refresh',
            'iat' => $now,
            'exp' => $now + 1800
        ], JSON_THROW_ON_ERROR);

        $header64 =  self::base64UrlEncode($header);
        $payload64 = self::base64UrlEncode($payload);

        $signature = hash_hmac('sha256', $header64. '.' . $payload64, PASSWORD_JWT, true);
        $signature64 = self::base64UrlEncode($signature);

        $token = $header64 . '.' . $payload64 . '.' . $signature64;

        return $token;
    }
}