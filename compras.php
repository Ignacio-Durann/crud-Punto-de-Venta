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
//este ya no tiene errores pero falta la operacion aritmetica en la insercion y falta modificar todo
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
$compra = new DataBase('compras');
$data = JWT::get_data($jwt, CONFIG::SECRET_JWT);
switch($_SERVER['REQUEST_METHOD']){
    case "GET":
        if(isset($_GET['idcompra'])){
           
            $where = array('idcompra'=>$_GET['idcompra']);
            $res = $compra->Read($where);
        }else{
            $res = $compra->ReadAll();
        }
        header("HTTP/1.1 200 OK");
        echo json_encode($res);
    break;
    case "POST":
        //insert into ventas values(123423,'pepsicola', 10,'el pepe', 3, vendido*precio,now());
        if(isset($_POST['idcompra']) && isset($_POST['idproductos']) && isset($_POST['nombrepro']) && isset($_POST['precio']) 
        && isset($_POST['comprador']) && isset($_POST['unidades']) && isset($_POST['total']) && isset($_POST['fecha']) ){
                       
            // $compra = new DataBase('compras');
            $datos = array(
                'idcompra'=>$_POST['idcompra'],
                'idproductos'=>$_POST['idproductos'],
                'nombrepro'=>$_POST['nombrepro'],
                'precio'=>$_POST['precio'],
                'comprador'=>$_POST['comprador'],
                'unidades'=>$_POST['unidades'],
                'total'=>$_POST['total'],
                'fecha'=>$_POST['fecha']
            );
            try{
                $reg = $compra->create($datos);
                $res = array("result"=>"ok","msg"=>"Se guardo la compra", "id"=>$reg);
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
        if(isset($_GET['idcompra']) && isset($_GET['idproductos']) && isset($_GET['nombrepro']) && isset($_GET['precio']) 
        && isset($_GET['comprador']) && isset($_GET['unidades']) && isset($_GET['total']) && isset($_GET['fecha']) ){
            
            $compra = new DataBase('compras');
            $where = array('idcompra'=>$_GET['idcompra']);
            $datos = array(
                //campos donde         campo dende se   
                //llegan datos          insertan datos los de la bd
                
                'idcompra'=>$_GET['idcompra'],
                'idproductos'=>$_GET['idproductos'],
                'nombrepro'=>$_GET['nombrepro'],
                'precio'=>$_GET['precio'],
                'comprador'=>$_GET['comprador'],
                'unidades'=>$_GET['unidades'],
                'total'=>$_GET['total'],
                'fecha'=>$_GET['fecha']
            );
            $reg = $compra->update($datos,$where);

            $res = array("result"=>"ok","msg"=>"Se guardo(actualizado) la compra", "num"=>$reg);
        
        }else{
            $res = array("result"=>"no","msg"=>"Faltan datos para la actualizacion");
        }
        echo json_encode($res);
    break;
    case "DELETE":
        if(isset($_GET['idcompra'])){
            
            $compra = new DataBase('compras');
            $where = array('idcompra'=>$_GET['idcompra']);
            $reg = $compra->delete($where);
            $res = array("result"=>"ok","msg"=>"Se elimino el producto", "num"=>$reg);
        
        }else{
            $res = array("result"=>"no","msg"=>"Faltan datos para la eliminacion");
        }
        echo json_encode($res);
    break;
    default:
        header("HTTP/1.1 401 Bad Request");
}