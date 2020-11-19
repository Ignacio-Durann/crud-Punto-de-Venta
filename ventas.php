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
//este ya no tiene errores
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
        if(isset($_GET['idventas'])){
            $ventas = new DataBase('ventas');
            $where = array('idventas'=>$_GET['idventas']);
            $res = $ventas->Read($where);
        }else{
            $ventas = new DataBase('ventas');
            $res = $ventas->ReadAll();
        }
        header("HTTP/1.1 200 OK");
        echo json_encode($res);
    break;
    case "POST":
        //insert into ventas values(123423,'pepsicola', 10,'el pepe', 3, vendido*precio,now());
        if(isset($_POST['idventas']) && isset($_POST['nombre']) && isset($_POST['vendido']) 
        && isset($_POST['vendio']) && isset($_POST['precio']) && isset($_POST['total']) && isset($_POST['fecha']) ){
                       
            $usuarios = new DataBase('ventas');
            $datos = array(
                'idventas'=>$_POST['idventas'],
                'nombre'=>$_POST['nombre'],
                'vendido'=>$_POST['vendido'],
                'vendio'=>$_POST['vendio'],
                'precio'=>$_POST['precio'],
                'total'=>$_POST['total'],
                'fecha'=>$_POST['fecha']
            );
            try{
                $reg = $usuarios->create($datos);
                $res = array("result"=>"ok","msg"=>"Se guardo el usuario", "id"=>$reg);
            }catch(PDOException $e){
                $res = array("result"=>"no","msg"=>$e->getMessage());
            }
                    
        }else{
            $res = array("result"=>"no","msg"=>"Faltan datos para la insercion");
        }
        header("HTTP/1.1 200 OK");
        echo json_encode($res);
    break;
    case "PUT":
        if(isset($_GET['idventas']) && isset($_GET['nombre']) && isset($_GET['vendido']) 
        && isset($_GET['vendio']) && isset($_GET['precio']) && isset($_GET['total']) && isset($_GET['fecha']) ){
            
            $ventas = new DataBase('ventas');
            $where = array('idventas'=>$_GET['idventas']);
            $datos = array(
                'idventas'=>$_GET['idventas'],
                'nombre'=>$_GET['nombre'],
                'vendido'=>$_GET['vendido'],
                'vendio'=>$_GET['vendio'],
                'precio'=>$_GET['precio'],
                'total'=>$_GET['total'],
                'fecha'=>$_GET['fecha']
            );
            $reg = $ventas->update($datos,$where);

            $res = array("result"=>"ok","msg"=>"Se guardo(actualizado) el registro", "num"=>$reg);
        
        }else{
            $res = array("result"=>"no","msg"=>"Faltan datos para la actualizacion");
        }
        echo json_encode($res);
    break;
    case "DELETE":
        if(isset($_GET['idventas'])){
            
            $ventas = new DataBase('ventas');
            $where = array('idventas'=>$_GET['idventas']);
            $reg = $ventas->delete($where);
            $res = array("result"=>"ok","msg"=>"Se elimino el registro", "num"=>$reg);
        
        }else{
            $res = array("result"=>"no","msg"=>"Faltan datos para la eliminacion");
        }
        echo json_encode($res);
    break;
    default:
        header("HTTP/1.1 401 Bad Request");
}