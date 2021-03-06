<?php
use \Firebase\JWT\JWT;

class Controller_Base extends Controller_Rest
{
	private static $secret_key = 'ARZOO';
    private static $encrypt = ['HS256'];
    private static $aud = null;
    private static $id_admin = 1;
    private static $id_user = 2;
    
    protected function respuesta($code, $message, $data = [])
    {
        $json = $this->response(array(
                    'code' => $code,
                    'message' => $message,
                    'data' => $data
                ));
            return $json;
    }
    protected function encode($data)
    {
        return  JWT::encode($data, $secret_key);
        
    }
    protected function decode($data)
    {
        return  JWT::decode($data, $secret_key, array('HS256'));
        
    }

	protected function encodeToken($userName, $password, $id, $email, $id_role, $profilePicture)
    {
        $token = array(
        		"id" => $id,
                "userName" => $userName,
                "password" => $password,
                "email" => $email,
                "role" => $id_role,
                "profilePicture" => $profilePicture,
        );
        $encodedToken = JWT::encode($token, $secret_key);
        return $encodedToken;
    }
    protected function decodeToken()
    {
        $header = apache_request_headers();
        $token = $header['Authorization'];
        if(!empty($token))
        {
            $decodedToken = JWT::decode($token, $secret_key, array('HS256'));
            return $decodedToken;
        }      
    }
    protected function authenticate(){
        try {
               
            $header = apache_request_headers();
            $token = $header['Authorization'];
            if(!empty($token))
            {
                $decodedToken = JWT::decode($token, $secret_key, array('HS256'));
                $query = Model_Users::find('all', 
                    ['where' => ['userName' => $decodedToken->userName, 
                                 'password' => $decodedToken->password, 
                                 'id_role' => $decodedToken->role,
                                 'email' => $decodedToken->email,
                                 'id' => $decodedToken->id
                                ]]);
                if($query != null)
                {
                    $json = array(
                    'code' => 200,
                    'message' => 'Usuario autenticado',
                    'authenticated' => true,
                    'data' => $token
                    );
                    return json_encode($json);

                }else{
                    $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Usuario no autenticado',
                    'authenticated' => false,
                    'data' => null
                    ));
                    return $json;
                
                }
            }else{
                $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Usuario no autenticado',
                    'authenticated' => false,
                    'data' => null
                    ));
                    return $json;
            }
        } 
        catch (Exception $UnexpectedValueException)
        {
            $json = $this->response(array(
                    'code' => 401,
                    'message' => 'Usuario no autenticado',
                    'authenticated' => false,
                    'data' => null
                    ));
                    return $json;
        }
    }
}
?>