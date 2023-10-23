<?php
    require("cors.php");
    require("queries.php");

    //Activacion de request POST de otros dominios
    cors();

    $queryTypes = array(
        "getUser",
        "getTemplate",
        "uploadTemplate",
        "loginUser",
        "registerUser"
    );

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Decodifica la query que manda NextJS
        $JsonQuery = json_decode(file_get_contents("php://input"));

        //Verifica que la query este dentro de la query disponibles
        if( !in_array($JsonQuery->query, $queryTypes) ) return errorResponse("La query no se encuentra entre los tipos de query validos");
        
        //Lee el tipo de query
        switch ($JsonQuery->query) {
            case 'registerUser':
                $credentials = $JsonQuery->credentials;

                $result = handleInsertion(array(
                    "table" => "usuario",
                    "type" => "registry",
                    "data" => array(
                        "nombres" => $credentials->nombres,
                        "apellido_p" => $credentials->apellido_p,
                        "apellido_m" => $credentials->apellido_m,
                        "correo" => $credentials->correo,
                        "contrasena" => $credentials->contrasena,
                        "telefono" => $credentials->telefono,
                        "ciudad" => $credentials->ciudad,
                        "codigo_postal" => $credentials->codigo_postal,
                        "pais" => $credentials->pais
                    )
                ));

                $response = $result;
                break;
            case 'loginUser':
                $credentials = $JsonQuery->credentials;
                
                $result = verifyPassword(array(
                    "table" => "usuario",
                    "correo" => $credentials->correo,
                    "contrasena" => $credentials->contrasena
                ));

                $response = $result;
                break;
            case 'uploadTemplate':
                $id = $JsonQuery->id;
                $template = $JsonQuery->template;

                $result = handleUpdate(array(
                    "table" => "organizacion",
                    "data" => array(
                        "pagina" => $template
                    ),
                    "where" => array(
                        "id" => $id
                    )
                ));

                $response = $result;
                break;
            default:
                $response = array(
                    "result" => "No hay query"
                );
                break;
        }

        // Devolver la respuesta en formato JSON
        header("Content-Type: application/json");
        echo json_encode($response);
    } else {
        // Si no es una solicitud POST, devolver un error
        header("HTTP/1.1 405 Method Not Allowed");
        echo json_encode(array("mensaje" => "Método no permitido"));
    }

    function errorResponse($errorMessage){
        $response = array(
            "result" => "error",
            "message" => $errorMessage
        );

        header("Content-Type: application/json");
        echo json_encode($response);
    }
?>

