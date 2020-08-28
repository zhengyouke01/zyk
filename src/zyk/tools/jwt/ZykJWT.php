<?php


/**
 * JWT
 * @author LYJ 2020.06.28
 */

namespace zyk\tools\jwt;

use think\App;
use think\Controller;
use Firebase\JWT;

class ZykJWT {

    //发放token
    static public function lssue($data = []){
        $token_data = self::lssue_jwt_token($data);
        $key = config('app.jwt.jwt_key');
        return JWT\JWT::encode($token_data, $key);
    }

    //验证token是否合法
    static public function verification($jwt = ''){
        if (!empty($jwt)) {
            $key = config('app.jwt.jwt_key');
            $data['msg'] = '';
            $data['code'] = -1;
            try {
                JWT\JWT::$leeway = 60;
                $decoded = JWT\JWT::decode($jwt, $key, ['HS256']);
                $arr = (array)$decoded;
                $data['data'] = $arr;
                $data['code'] = 200;
            }catch(JWT\SignatureInvalidException $e) {
                $data['msg'] = $e->getMessage();
            }catch(JWT\BeforeValidException $e) {
                $data['msg'] = $e->getMessage();
            }catch(JWT\ExpiredException $e) {
                $data['msg'] = $e->getMessage();
            }catch(\ErrorException $e) {
                $data['msg'] = $e->getMessage();
            }
        }else {
            $data['msg'] = '';
            $data['code'] = -1;
        }
        return $data;
    }


    //签发token
    static private function lssue_jwt_token($data = []) {
        $time          = TIME_NOW; //当前时间
        $token         = config("app.jwt.jwt_token");
        $exp           = config('app.jwt.jwt_expire');
        $token['iat']  = $time;
        $token['nbf']  = $time;
        if (!empty($exp)) {
            $token['exp'] = $time + $exp;
        }
        $token['data'] = $data;
        return $token;
    }

}
