<?php
require_once("../QueriesHandler.php"); // Reemplaza "tu_archivo.php" con el nombre de tu archivo que contiene las funciones
/*
// Prueba de handleInsertion
$jsonInsert = '{
    "table": "Usuario",
    "data": {
        "nombres": "John",
        "apellido_p": "Doe",
        "apellido_m": "Smith",
        "correo": "john@example.com",
        "contrasena": "hashed_password",
        "telefono": "123456789",
        "ciudad": "New York",
        "codigo_postal": "10001",
        "pais": "USA"
    }
}';
if (handleInsertion($jsonInsert)) {
    echo "Prueba de inserción PASADA.\n";
} else {
    echo "Prueba de inserción FALLIDA.\n";
}

// Prueba de handleSelection
$jsonSelect = '{
    "table": "Usuario",
    "select": ["nombres", "correo"],
    "where": "ciudad = \'New York\'",
    "limit": 10
}';
$resultSelection = handleSelection($jsonSelect);
if (is_array($resultSelection) && count($resultSelection) > 0) {
    echo "Prueba de selección PASADA.\n";
} else {
    echo "Prueba de selección FALLIDA.\n";
}

// Prueba de handleUpdate
$jsonUpdate = '{
    "table": "Usuario",
    "data": {
        "nombres": "Updated Name"
    },
    "where": "ciudad = \'New York\'"
}';
if (handleUpdate($jsonUpdate)) {
    echo "Prueba de actualización PASADA.\n";
} else {
    echo "Prueba de actualización FALLIDA.\n";
}

// Prueba de handleDelete
$jsonDelete = '{
    "table": "Usuario",
    "where": "ciudad = \'New York\'"
}';
if (handleDelete($jsonDelete)) {
    echo "Prueba de eliminación PASADA.\n";
} else {
    echo "Prueba de eliminación FALLIDA.\n";
}*/

/*require("./queries.php");

$dic = array(
    "table" => "organizacion",
    "where" => array(
        "id" => 2
    ),
    "data" => array(
        "template" => json_encode(array(
            "columna1" => "valor1",
            "columna2" => "valor2",
            "columna3" => "valor3",
        ))
    )
);

$result = handleUpdate($dic);

echo "<br><br>";
print_r($result);
echo "<br><br>";

$dic = array(
    "table" => "organizacion",
    "where" => array(
        "id" => 2
    ),
    "select" => array("template")
);

$result = handleSelection($dic);

$var = json_decode($result[0]["template"]);

echo "<br><br>";
print_r($var->columna1);
echo "<br><br>";

$template = array(
    "component1" => "valor1",
    "component2" => "valor2",
    "component3" => "valor3",
    "component4" => "valor4",
);

$dic = array(
    "table" => "organizacion",
    "data" => array(
        "template" => json_encode($template)
    ),
    "where" => array(
        "id" => 1
    )
);

$result = handleUpdate($dic);

echo "<br><br> Resultado:";
print_r($result);
echo "<br><br>";

/*$sql = "WHERE ";
foreach ($dic["where"] as $key => $value) {
    if(is_string($value)){
        $sql = $sql."$key = '$value'";
    }else{
        $sql = $sql."$key = $value";
    }
    if($key != array_key_last($dic["where"])){
        $sql = $sql." AND ";
    }
}

echo $sql;*/

// Function to get the client IP address
function get_client_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
       $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

echo get_client_ip();


?>