<?php
require_once("DbConnection.php");

function handleInsertion($json) {
    $dataArray = $json['data'];
    $tableName = $json['table'];

    // Verificar si se está realizando un registro y si existe el correo
    if (isset($json['type']) && $json['type'] === 'registry' && isset($dataArray['correo'])) {
        $correo = $dataArray['correo'];
        $checkQuery = "SELECT COUNT(*) as count FROM $tableName WHERE correo = '$correo'";
        $result = exeQuery($checkQuery);
        $row = mysqli_fetch_assoc($result);

        if ($row['count'] > 0) {
            return array(
                "result" => "error",
                "message" => "Error: El ususario ya esta registrado"
            );
        }
    }

    if (isset($dataArray['contrasena'])) {
        $plainPassword = $dataArray['contrasena'];
        $hashedPassword = password_hash($plainPassword, PASSWORD_DEFAULT, ['cost' => 15]);
        $dataArray['contrasena'] = $hashedPassword;
    }

    $columns = implode(", ", array_keys($dataArray));//get the columns
    $values = implode("', '", array_values($dataArray));//get the values
    $sql = "INSERT INTO $tableName ($columns) VALUES ('$values');";

    $result = exeQuery($sql);

    if ($result) {
        return array(
            "result" => "success",
            "message" => "Insercion completada correctamente"
        );
    }
    
    return array(
        "result" => "error",
        "message" => "Error: No se pudo completar la insercion"
    );
}

function handleSelection($json) {
    $dataArray = $json['select'] ?? ['*'];
    $tableName = $json['table'];
    $where = $json['where'] ?? '';
    $limit = $json['limit'] ?? '';

    $selectedColumns = implode(", ", $dataArray);//get the columns
    $sql = "SELECT DISTINCT $selectedColumns FROM $tableName";//get the values
    
    if($where != ""){
        $sql = $sql." WHERE ";
        foreach ($where as $key => $value) {
            if(is_string($value)){
                $sql = $sql."$key = '$value'";
            }else{
                $sql = $sql."$key = $value";
            }
            if($key != array_key_last($where)){
                $sql = $sql." AND ";
            }
        }
    }
    if (!empty($limit)) $sql = $sql." LIMIT $limit";//if there is a limit clause
    
    $result = exeQuery($sql);

    return mysqli_fetch_all($result, MYSQLI_ASSOC);//get the rows as an associative array
}

function verifyPassword($json) {
    $tableName = $json['table'];
    $userMail = $json['correo'];
    $providedPassword = $json['contrasena'];

    $sql = "SELECT contrasena FROM $tableName WHERE correo = '$userMail'";
    $result = exeQuery($sql);
    
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $hashedPassword = $row['contrasena'];
        if (password_verify($providedPassword, $hashedPassword)) {
            return array(
                "result" => "success",
                "message" => "Credenciales verificadas correctamente"
            );
        }else{
            return array(
                "result" => "error",
                "message" => "Error: Credenciales invalidas"
            );
        }
    }

    return array(
        "result" => "error",
        "message" => "Error: No se pudo completar la verificacion de credenciales"
    );
}


function handleUpdate($json) {
    $tableName = $json['table'];
    $dataArray = $json['data'];
    $where = $json['where'];

    $updateColumns = [];//array to store the columns to update
    foreach ($dataArray as $column => $value) {//for each column and value
        $updateColumns[] = "$column = '$value'";//add the column and value to the array
    }
    $setClause = implode(", ", $updateColumns);//separate the columns and values with a comma

    $sql = "UPDATE $tableName SET $setClause";//create the sql query

    if($where != ""){
        $sql = $sql." WHERE ";
        foreach ($where as $key => $value) {
            if(is_string($value)){
                $sql = $sql."$key = '$value'";
            }else{
                $sql = $sql."$key = $value";
            }
            if($key != array_key_last($where)){
                $sql = $sql." AND ";
            }
        }
    }

    if(exeQuery($sql)){
        return array(
            "result" => "success",
            "message" => "Template actualizada correctamente"
        );
    }

    return array(
        "result" => "error",
        "message" => "Hubo un error al acceder a la base de datos"
    );
}

function handleDelete($json) {
    $tableName = $json['table'];
    $where = $json['where'];

    $whereClause = (is_array($where)) ? implode(' AND ', $where) : $where;//if the where condition is an array, implode it with 'AND'

    $sql = "DELETE FROM $tableName WHERE '$whereClause'";//create the sql query to delete the rows

    if(exeQuery($sql)){
        return array(
            "result" => "success",
            "message" => "Eliminacion completada exitosamente"
        );
    }

    return array(
        "result" => "error",
        "message" => "Error: No se pudo completar la eliminacion"
    );;
}

function exeQuery($setence) {
    global $conection;//get the connection variable

    $result = mysqli_query($conection, $setence);

    return $result;
}
?>