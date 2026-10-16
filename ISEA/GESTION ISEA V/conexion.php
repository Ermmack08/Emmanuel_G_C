<?php
$conexion = mysqli_connect("localhost", "root", "", "isea");

if (mysqli_connect_errno()) {
    throw new RuntimeException('Mysqli connetion error: '. mysqli_connect_error());
}else{
    // echo "Conexión exitosa a la base de datos.";
}