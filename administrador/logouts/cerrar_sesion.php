<?php
session_start();

require_once('../../variable_global.php');
require_once(ROOT_PATH . '/administrador/conexion.php');


if (isset($_POST['cerrar_sesion'])) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id_adm_loguado = $_SESSION['id_usuario_adm'] ?? null;
        $id_cliente_loguado = $_SESSION['id_usuario_cliente'] ?? null;
        
        if ($id_adm_loguado || $id_cliente_loguado) {
            $id_usuario = $id_adm_loguado ?? $id_cliente_loguado;

            
            $seleccionar_tipo_usuario = $mysqli->prepare("
                SELECT usuarios.id_usuario, usuarios.usuario, usuarios.clave, usuarios.correo, usuarios.imagen_usuario, tipo_usuario.tipo_usuario 
                FROM usuarios
                INNER JOIN tipo_usuario ON usuarios.id_tipo_usuario = tipo_usuario.id_tipo_usuario
                WHERE id_usuario = ?");
            $seleccionar_tipo_usuario->bind_param("i", $id_usuario);
            $seleccionar_tipo_usuario->execute();
            $seleccionar_tipo_usuario->store_result();


            if ($seleccionar_tipo_usuario->num_rows > 0) {
                $seleccionar_tipo_usuario->bind_result($id_usuario, $usuario_nombre, $clave, $correo, $imagen_usuario, $tipo_usuario);
                $seleccionar_tipo_usuario->fetch();

                $fecha_logueada = date('Y-m-d H:i:s');

                $buscar_historial = $mysqli->prepare("
                    SELECT id_historial_logeos 
                    FROM historial_logeos 
                    WHERE id_usuario = ? AND fecha_cerro IS NULL 
                    ORDER BY fecha_inicio DESC 
                    LIMIT 1;
                ");
                $buscar_historial->bind_param("i", $id_usuario);
                $buscar_historial->execute();
                $buscar_historial->bind_result($id_historial);
                $buscar_historial->fetch();
                $buscar_historial->close();

                

                if ($id_historial) {
                    $actualizar_logueo = $mysqli->prepare("
                        UPDATE historial_logeos 
                        SET fecha_cerro = ? 
                        WHERE id_historial_logeos = ?
                    ");
                    $actualizar_logueo->bind_param("si", $fecha_logueada, $id_historial);
                    $actualizar_logueo->execute();
                    $actualizar_logueo->close();
                }

                
                session_unset();
                session_destroy();

                
                header("Location: " . BASE_URL . "/administrador/login/login.php");
                exit;
            } else {
               
                echo '<script>
                    alert("Hubo un fallo al cerrar sesión");
                    self.location = "' . BASE_URL . '/administrador/login/login.php";
                </script>';
                exit;
            }
        } else {
            
            echo '<script>
                alert("No hay sesión activa para cerrar");
                self.location = "' . BASE_URL . '/administrador/login/login.php";
            </script>';
            exit;
        }
    }
}
?>
