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
$data = JWT::get_data($jwt, CONFIG::SECRET_JWT);
switch($_SERVER['REQUEST_METHOD']){
    case "GET":
        if(isset($_GET['idproducto'])){
            $productos = new DataBase('productos');
            $where = array('idproducto'=>$_GET['idproducto']);
            $res = $productos->Read($where);
        }else{
            $productos = new DataBase('productos');
            $res = $productos->ReadAll();
        }
        header("HTTP/1.1 200 OK");
        echo json_encode($res);
    break;
    case "POST":
        //insert into ventas values(123423,'pepsicola', 10,'el pepe', 3, vendido*precio,now());
        if(isset($_POST['idproducto']) && isset($_POST['nombreproducto']) && isset($_POST['precio']) 
        && isset($_POST['comprador']) && isset($_POST['unidadescompradas']) && isset($_POST['totalpagar']) && isset($_POST['fecha']) ){
                       
            $productos = new DataBase('productos');
            $datos = array(
                'idproducto'=>$_POST['idproducto'],
                'nombreproducto'=>$_POST['nombreproducto'],
                'precio'=>$_POST['precio'],
                'comprador'=>$_POST['comprador'],
                'unidadescompradas'=>$_POST['unidadescompradas'],
                'totalpagar'=>$_POST['totalpagar'],
                'fecha'=>$_POST['fecha']
            );
            try{
                $reg = $productos->create($datos);
                $res = array("result"=>"ok","msg"=>"Se guardo el producto", "id"=>$reg);
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
        if(isset($_GET['idproducto']) && isset($_GET['nombreproducto']) && isset($_GET['precio']) 
        && isset($_GET['comprador']) && isset($_GET['unidadescompradas']) && isset($_GET['totalpagar']) && isset($_GET['fecha']) ){
            
            $productos = new DataBase('productos');
            $where = array('idproducto'=>$_GET['idproducto']);
            $datos = array(
                //campos donde         campo dende se   
                //llegan datos          insertan datos los de la bd
                'idproducto'=>$_GET['idproducto'],
                'nombreproducto'=>$_GET['nombreproducto'],
                'precio'=>$_GET['precio'],
                'comprador'=>$_GET['comprador'],
                'unidadescompradas'=>$_GET['unidadescompradas'],
                'totalpagar'=>$_GET['totalpagar'],
                'fecha'=>$_GET['fecha']
            );
            $reg = $productos->update($datos,$where);

            $res = array("result"=>"ok","msg"=>"Se guardo(actualizado) el producto", "num"=>$reg);
        
        }else{
            $res = array("result"=>"no","msg"=>"Faltan datos para la actualizacion");
        }
        echo json_encode($res);
    break;
    case "DELETE":
        if(isset($_GET['idproducto'])){
            
            $productos = new DataBase('productos');
            $where = array('idproducto'=>$_GET['idproducto']);
            $reg = $productos->delete($where);
            $res = array("result"=>"ok","msg"=>"Se elimino el producto", "num"=>$reg);
        
        }else{
            $res = array("result"=>"no","msg"=>"Faltan datos para la eliminacion");
        }
        echo json_encode($res);
    break;
    default:
        header("HTTP/1.1 401 Bad Request");
}