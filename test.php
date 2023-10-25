<?php
/*require_once("queries.php"); // Reemplaza "tu_archivo.php" con el nombre de tu archivo que contiene las funciones

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

$where = array(
    "username" => "nestor",
    "password" => "hola"
);

foreach ($where as $key => $value) {
    $query = $query . "AND" . $key . "=" . "'$'"
    echo $key;
}

?>