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

                $response = handleInsertion(array(
                    "table" => "usuario",
                    "type" => "registry",
                    "data" => $credentials
                ));
                break;
            case 'loginUser':
                $credentials = $JsonQuery->credentials;
                
                $response = verifyPassword(array(
                    "table" => "usuario",
                    "correo" => $credentials->correo,
                    "contrasena" => $credentials->contrasena
                ));
                break;
            case 'uploadTemplate':
                $id = $JsonQuery->organization;
                $template = $JsonQuery->template;

                $response = handleUpdate(array(
                    "table" => "organizacion",
                    "data" => array(
                        "template" => json_encode($template)
                    ),
                    "where" => array(
                        "id" => $id
                    )
                ));
                break;
            case 'getTemplate':
                $id = $JsonQuery->organization;
                
                $data = handleSelection(array(
                    "table" => "organizacion",
                    "select" => array("template", "ciudad", "pais"),
                    "where" => array(
                        "id" => $id
                    )
                ));

                $response = array(
                    "result" => "success",
                    "template" => json_decode($data[0]["template"], true)
                );

                break;
            case 'getUser':
                $userType = $JsonQuery->userType;
                $select = $JsonQuery->select ?? "";
                $where = $JsonQuery->where ?? "";
                $limit = $JsonQuery->limit ?? "";

                $data = handleSelection(array(
                    "table" => $userType,
                    "select" => $select,
                    "where" => $where,
                    "limit" => $limit
                ));
                
                break;
            default:
                return errorResponse("La query no se encuentra entre los tipos de query validas");
                break;
        }

        // Devolver la respuesta en formato JSON
        header("Content-Type: application/json");
        echo json_encode($response);
    } else {
        // Si no es una solicitud POST, devolver un error
        errorResponse("Metodo no permitido");
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

