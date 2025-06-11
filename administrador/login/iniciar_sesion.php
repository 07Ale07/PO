<?php
require_once('../../variable_global.php');
    
require_once(ROOT_PATH . '/administrador/conexion.php');

require_once(ROOT_PATH . '/enviar_mail_php-main/vendor/autoload.php');
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


if(isset($_POST['iniciar_sesion']) && $_POST['iniciar_sesion']){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $usuario = $_POST['nombre_usuario'];
        $contrasena = $_POST['contrasena'];

        $consultar_usuarios = $mysqli->prepare("SELECT usuarios.id_usuario, usuarios.usuario, usuarios.clave, usuarios.correo, tipo_usuario.tipo_usuario FROM usuarios
        INNER JOIN tipo_usuario ON tipo_usuario.id_tipo_usuario = usuarios.id_tipo_usuario
        WHERE usuarios.usuario = ? AND usuarios.clave = ?");

        $consultar_usuarios->bind_param("ss",$usuario,$contrasena);
        $consultar_usuarios->execute();
        $consultar_usuarios->store_result();

        if($consultar_usuarios->num_rows > 0){
            $usuario_admin = $mysqli->prepare("SELECT usuarios.id_usuario, usuarios.usuario, usuarios.clave, usuarios.correo, tipo_usuario.tipo_usuario FROM usuarios
            INNER JOIN tipo_usuario ON tipo_usuario.id_tipo_usuario = usuarios.id_tipo_usuario
            WHERE tipo_usuario = 'admin' AND usuarios.usuario = ? AND usuarios.clave = ?");
            $usuario_admin->bind_param("ss",$usuario,$contrasena);
            $usuario_admin->execute();
            $usuario_admin->store_result();

            $usuario_cliente = $mysqli->prepare("SELECT usuarios.id_usuario, usuarios.usuario, usuarios.clave, usuarios.correo, tipo_usuario.tipo_usuario FROM usuarios
            INNER JOIN tipo_usuario ON tipo_usuario.id_tipo_usuario = usuarios.id_tipo_usuario
            WHERE tipo_usuario = 'cliente' AND usuarios.usuario = ? AND usuarios.clave = ?");
            $usuario_cliente->bind_param("ss",$usuario,$contrasena);
            $usuario_cliente->execute();
            $usuario_cliente->store_result();

            if($usuario_admin->num_rows > 0){
                $usuario_admin->bind_result = ($id_usuario_adm,$usuario_nombre_adm,$clave_adm,$correo_adm,$tipo_usuario_adm);
                $usuario_admin->fetch();

                $mail = new PHPMailer(true);

                try {
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'su correo';
                    $mail->Password = 'su clave privada';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('su correo', 'Prueba');
                    $mail->addAddress($correo_adm, 'Nuevo inicio de sesion en inter city turismo');
                    $mail->addCC('concopia@gmail.com');

                    $mail->addAttachment(ROOT_PATH . '/enviar_mail_php-main/docs/dashboard.png', 'Dashboard.png');

                    $mail->isHTML(true);
                    $mail->Subject = 'Prueba desde IL';
                    $mail->Body = 'Hola, Alguien a accedido a tu cuenta de inter cyty<br/>Esta es una prueba desde <b>Gmail</b>.';
                    $mail->send();

                    echo 'Correo enviado';
                } catch (Exception $e) {
                    echo 'Mensaje ' . $mail->ErrorInfo;
                }


                header("Location: " . BASE_URL . "/administrador/login/inicio_adm.php");
                
                exit;

            }elseif($usuario_cliente->num_rows > 0){

                $usuario_cliente->bind_result = ($id_usuario_cliente,$usuario_nombre_cliente,$clave_cliente,$correo_cliente,$tipo_usuario_cliente);
                $usuario_cliente->fetch();
                $mail = new PHPMailer(true);

                try {
                    $mail->SMTPDebug = 0;
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'vega98790@gmail.com';
                    $mail->Password = 'paku dqto qkbx nhgv ';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    $mail->setFrom('vega98790@gmail.com', 'Prueba');
                    $mail->addAddress($correo_cliente, 'Nuevo inicio de sesion en inter city turismo');
                    $mail->addCC('concopia@gmail.com');


                    $mail->isHTML(true);
                    $mail->Subject = 'Prueba desde IL';
                    $mail->Body = 'Hola, Alguien a accedido a tu cuenta de inter cyty<br/>Esta es una prueba desde <b>Gmail</b>.';
                    $mail->send();

                    echo 'Correo enviado';
                } catch (Exception $e) {
                    echo 'Mensaje ' . $mail->ErrorInfo;
                }


                header("Location: " . BASE_URL . "/administrador/login/inicio_cliente.php");
                exit;
            }else{
                echo '<script language = javascript>
                alert("su usuario no tiene rol, intentelo de nuevo")
                self.location = "' . BASE_URL . '/administrador/login/login.php"
                </script>';
                exit;
            }
        }else{
            echo '<script language = javascript>
            alert("no existe el usuario")
            self.location = "' . BASE_URL . '/administrador/login/login.php"
            </script>';
            exit;
        }


    }else{
        echo '<script language = javascript>
                alert("hubo un fallo en el al enviar por post")
                self.location = "' . BASE_URL . '/administrador/login/login.php"
                </script>';
                exit;
    }

}else{
    echo '<script language = javascript>
                alert("hubo un fallo al enviar")
                self.location = "' . BASE_URL . '/administrador/login/login.php"
                </script>';
                exit;
}
?>