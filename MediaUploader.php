<?php

require("QueriesHandler.php");
require("PhpHeadersModifier.php");

modifyHeaders();

// Inicializar diccionarios globales
$templateComponents = [];
$contactUrls = [];
$mediaUrls = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener el userID y el número de fotos
    $userId = $_POST["userId"];
    $numberOfPhotos = $_POST["numberOfPhotos"];

    // Directorio donde se guardarán las fotos
    $uploadDir = "media/images/template/$userId/";

    // Crear directorio si no existe
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Procesar cada foto
    for ($i = 1; $i <= $numberOfPhotos; $i++) {
        $inputName = "media$i";

        // Verificar si se seleccionó un archivo
        if (!empty($_FILES[$inputName]['name'])) {
            $fileName = "media$i.png";
            $filePath = $uploadDir . $fileName;

            // Mover la foto al directorio especificado
            move_uploaded_file($_FILES[$inputName]['tmp_name'], $filePath);

            // Agregar la clave y el valor al diccionario de mediaUrls
            $mediaUrls[$inputName] = "http://localhost:80/api/" . $uploadDir . $fileName;
        }
    }

    // Recorrer los datos del formulario
    foreach ($_POST as $key => $value) {
        // Verificar si la clave contiene "contactUrl"
        if (strpos($key, "contactUrl") !== false) {
            // Agregar la clave y el valor al diccionario de contactUrls
            $contactUrls[$key] = $value;
        } elseif (strpos($key, "media") !== false || $key === "userId" || $key === "numberOfPhotos" || $key === "templateId") {
            // La información de la imagen ya se agregó en el bucle anterior
        } else {
            // Agregar la clave y el valor al diccionario de templateComponents
            $templateComponents[$key] = $value;
        }
    }

    // Puedes imprimir o manipular los diccionarios según tus necesidades
    echo "Template Components: ";
    print_r($templateComponents);
    echo "<br><br>";

    echo "Contact Urls: ";
    print_r($contactUrls);
    echo "<br><br>";

    echo "Media Urls: ";
    print_r($mediaUrls);
    echo "<br><br>";

    $dic = array(
        "table" => "organizacion",
        "data" => array(
            "template" => json_encode(array(
                "templateId" => $_POST["templateId"],
                "components" => $templateComponents,
                "contactUrls" => $contactUrls,
                "mediaUrls" => $mediaUrls
            ))
        ),
        "where" => array(
            "id" => 1
        )
    );
    
    $result = handleUpdate($dic);

    echo $result;

    echo "Datos procesados con éxito.";
} else {
    echo "Acceso no permitido.";
}
?>