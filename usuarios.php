<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, Access-Control-Allow-Methods, Access-Control-Allow-Headers, Allow, Access-Control-Allow-Origin");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS, HEAD");
header("Allow: GET, POST, PUT, DELETE, OPTIONS, HEAD");
require_once 'database.php';
require_once 'jwt.php';
if($_SERVER['REQUEST_METHOD']=="OPTIONS"){
    exit();
}
//este ya no tiene errores pero falta la operacion aritmetica en la insercion
$header = apache_request_headers();
$jwt = trim($header['Authorization']);
switch (JWT::verify($jwt, CONFIG::SECRET_JWT)) {
    case 1:
        header("HTTP/1.1 401 Unauthorized");
        echo "El token no es válido";
        exit();
        break;
    case 2:
        header("HTTP/1.1 408 Request Timeout");
        echo "La sesión caduco";
        exit();
        break;
}

switch($_SERVER['REQUEST_METHOD']){
    case "GET":
        if(isset($_GET['user'])){
            $usuarios = new DataBase('usuarios');
            $where = array('user'=>$_GET['user']);
            $res = $usuarios->Read($where);
        }else{
            $usuarios = new DataBase('usuarios');
            $res = $usuarios->ReadAll();
        }
        header("HTTP/1.1 200 OK");
        echo json_encode($res);
    break;
    case "POST":
        if(isset($_POST['user']) && isset($_POST['nombre']) && isset($_POST['password']) 
        && isset($_POST['tipo']) && isset($_POST['edad']) && isset($_POST['puesto']) ){
            
            $usuarios = new DataBase('usuarios');
            $datos = array(
                'user'=>$_POST['user'],
                'nombre'=>$_POST['nombre'],
                'password'=>$_POST['password'],
                'tipo'=>$_POST['tipo'],
                'edad'=>$_POST['edad'],
                'puesto'=>$_POST['puesto'],
            );
            try{
                $reg = $usuarios->create($datos);
                $res = array("result"=>"ok","msg"=>"Se guardo el usuario", "id"=>$reg);
            }catch(PDOException $e){
                $res = array("result"=>"no","msg"=>$e->getMessage());
            }
                    
        }else{
            $res = array("result"=>"no","msg"=>"Faltan datos post");
        }
        header("HTTP/1.1 200 OK");
        echo json_encode($res);
    break;
    case "PUT":
        if(isset($_GET['user']) && isset($_GET['nombre']) && isset($_GET['password']) 
        && isset($_GET['tipo']) && isset($_GET['edad']) && isset($_GET['puesto'])){
            
            $usuarios = new DataBase('usuarios');
            $where = array('user'=>$_GET['user']);
            $datos = array(
                'user'=>$_GET['user'],
                'nombre'=>$_GET['nombre'],
                'password'=>$_GET['password'],
                'tipo'=>$_GET['tipo'],
                'edad'=>$_GET['edad'],
                'puesto'=>$_GET['puesto']
            );
            $reg = $usuarios->update($datos,$where);

            $res = array("result"=>"ok","msg"=>"Se guardo(actualizado) el usuario", "num"=>$reg);
        
        }else{
            $res = array("result"=>"no","msg"=>"Faltan datos put");
        }
        echo json_encode($res);
    break;
    case "DELETE":
        if(isset($_GET['user'])){
            
            $usuarios = new DataBase('usuarios');
            $where = array('user'=>$_GET['user']);
            $reg = $usuarios->delete($where);
            $res = array("result"=>"ok","msg"=>"Se elimino el tema", "num"=>$reg);
        
        }else{
            $res = array("result"=>"no","msg"=>"Faltan datos");
        }
        echo json_encode($res);
    break;
    default:
        header("HTTP/1.1 401 Bad Request");
}
