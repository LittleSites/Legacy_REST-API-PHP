<?php 
    $conection = mysqli_connect('127.0.0.1', 'root', '', 'littlesites_db');

    if(!$conection) echo 'Error en la conexión';
?>