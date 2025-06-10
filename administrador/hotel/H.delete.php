<?php
require '../conexion.php';
$id_hotel=$_GET['codigo'];

$delete="DELETE FROM hoteles WHERE id_hotel='$id_hotel'";

$result= $mysqli->query($delete);

echo '<script language = javascript>
    alert("Se elimino la información correctamente, redireccionando")
    self.location = "start.php"
    </script>';
?>