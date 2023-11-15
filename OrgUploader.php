<?php 

require("QueriesHandler.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    // Obtener el userID y el número de fotos
    $orgId = $_POST["orgId"];
    $nombre = $_POST["nombre"];

    // Directorio donde se guardarán las fotos
    $uploadDir = "media/images/org/$orgId/$nombre/";

    // Crear directorio si no existe
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Procesar cada foto
    $inputName = "imagen";

        // Verificar si se seleccionó un archivo
    if (!empty($_FILES[$inputName]['name'])) {
        $fileName = "porfile.png";
        $filePath = $uploadDir . $fileName;

        // Mover la foto al directorio especificado
        move_uploaded_file($_FILES[$inputName]['tmp_name'], $filePath);

        // Agregar la clave y el valor al diccionario de mediaUrls
        $mediaUrls[$inputName] = "http://localhost:80/api/" . $uploadDir . $fileName;
    }

    $dic = array(
        "table" => "organizacion",
        "data" => array(
            "img" => $filePath
        ),
        "where" => array(
            "responsable" => $orgId,
            "nombre_organizacion" => $nombre
        )
    );
    
    $result = handleUpdate($dic);

    echo $result;
}

?>