<?php
require_once("connect.php");

/*
    * Nestor Example
    * "CREATE" : {
    *   "table" : "user",
    *   "data" : {
    *       "col1":"val1",
    *       "col2":"val2",
    *       "col3":"val3"
    *   }
    * }
    * "READ" : {
    *   "table" : "user",
    *   "limit" : 10,
    *   "where" : "col1 = 'val1'",
    *   "select" : ["col1", "col2", "col3"]
    * }
    * "UPDATE" : {
    *   "table" : "user",
    *   "data" : {
    *       "col1":"val1",
    *       "col2":"val2",
    *       "col3":"val3"
    *   },
    *   "where" : "col1 = 'val1'"
    * }
    * "DELETE" : {
    *   "table" : "user",
    *   "where" : "col1 = 'val1'"
    * }
*/

function handleJson($json, $fieldsToRetrieve) {
    $dataArray = json_decode($json, true);//true to return an associative array

    if ($dataArray === null) {
        echo "Error: JSON no válido\n";
        return null;
    }

    $result = array();//array to return

    foreach ($fieldsToRetrieve as $field) {
        if ($field === 'data' && isset($dataArray['data'])) {
            $result['data'] = $dataArray['data'];
        } else {
            $result[$field] = $dataArray[$field] ?? null;
        }
    }

    return $result;
}

function handleInsertion($response) {
    $dataArray = $response['data'];
    $tableName = $response['table'];

    // Verificar si se está realizando un registro y si existe el correo
    if ($response['type'] === 'registry' && isset($dataArray['correo'])) {
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

function handleSelection($response) {
    $dataArray = $response['select'] ?? ['*'];
    $where = $response['where'] ?? '';
    $limit = $response['limit'] ?? '';

    $selectedColumns = implode(", ", $dataArray);//get the columns
    $sql = "SELECT DISTINCT $selectedColumns FROM $tableName";//get the values
    if (!empty($where)) $sql = $sql." WHERE $where";//if there is a where clause
    if (!empty($limit)) $sql = $sql." LIMIT $limit";//if there is a limit clause
    $result = exeQuery($sql);

    /* 
    * Example of how to use the result
    * $rows = [
    * ['id' => '1', 'nombre' => 'Juan', 'edad' => '30'],
    * ['id' => '2', 'nombre' => 'Maria', 'edad' => '25'],
    * ['id' => '3', 'nombre' => 'Pedro', 'edad' => '35']
    * ];
    * */
    return mysqli_fetch_all($result, MYSQLI_ASSOC);//get the rows as an associative array
}

function verifyPassword($response) {
    $tableName = $response['table'];
    $userMail = $response['correo'];
    $providedPassword = $response['contrasena'];

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
    $response = handleJson($json, ['table', 'data', 'where']);//get the table name and the data array
    $tableName = $response['table'];
    $dataArray = $response['data'];
    $where = $response['where'];

    $updateColumns = [];//array to store the columns to update
    foreach ($dataArray as $column => $value) {//for each column and value
        $updateColumns[] = "$column = '$value'";//add the column and value to the array
    }
    $setClause = implode(", ", $updateColumns);//separate the columns and values with a comma

    $whereClause = (is_array($where)) ? implode(' AND ', $where) : $where;//if the where condition is an array, implode it with 'AND'

    $sql = "UPDATE $tableName SET $setClause WHERE $whereClause";//create the sql query

    return exeQuery($sql);
}

function handleDelete($response) {
    $tableName = $response['table'];
    $whereCondition = $response['where'];

    $sql = "DELETE FROM $tableName WHERE $whereCondition";//create the sql query to delete the rows

    $result = exeQuery($sql);

    if($result){
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