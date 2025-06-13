<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../variable_global.php');
    
require_once(ROOT_PATH . '/administrador/conexion.php');

if(isset($_POST['subir_nuevo_usuario']) && $_POST['subir_nuevo_usuario']){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $usuario = $_POST['nombre_usuario'];
        $contrasena = $_POST['contrasena'];
        $email = $_POST['correo_electronico'];

        $insertar_nuevo_usuario = $conexion->prepare("INSERT INTO usuarios(usuario, clave, correo, id_tipo_usuario) VALUES (?,?,?,2)");
        $insertar_nuevo_usuario->bind_param("sss",$usuario,$contrasena,$email);
        
        if($insertar_nuevo_usuario->execute()){
            echo '<script language = javascript>
                alert("nuevo usuario agregado correctamente")
                self.location = "' . BASE_URL . '/administrador/login/login.php"
                </script>';
                exit;

        }else{
            echo '<script language = javascript>
                alert("hubo un fallo en la creacion de un nuevo usuario,intentelo de nuevo")
                self.location = "' . BASE_URL . '/administrador/login/login.php"
                </script>';
                exit;
        }


    }else{
        echo '<script language = javascript>
                alert("hubo un fallo al enviar por post")
                self.location = "' . BASE_URL . '/administrador/login/login.php"
                </script>';
                exit;
    }

}else{
    echo '<script language = javascript>
                alert("hubo un fallo al enviar en el servidor al enviar por post")
                self.location = "' . BASE_URL . '/administrador/login/login.php"
                </script>';
                exit;
}

?>