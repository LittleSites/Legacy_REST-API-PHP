<?php
    require("PhpHeadersModifier.php");
    require("QueriesHandler.php");
    require("SessionManager.php");

    //Activacion de request POST de otros dominios
    modifyHeaders();

    $queryTypes = array(
        "getUser",
        "getTemplate",
        "uploadTemplate",
        "loginUser",
        "registerUser"
    );

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        //Decodifica la query que manda NextJS
        $JsonQuery = json_decode(file_get_contents("php://input"), true);

        //Verifica que la query este dentro de la query disponibles
        if( !in_array($JsonQuery["query"], $queryTypes) ) return errorResponse("La query no se encuentra entre los tipos de query validos");
        
        //Lee el tipo de query
        switch ($JsonQuery["query"]) {
            case 'registerUser':
                $userType = $JsonQuery["userType"];
                $credentials = $JsonQuery["credentials"];
                $userType = $JsonQuery["userType"];

                $response = handleInsertion(array(
                    "table" => $userType,
                    "type" => "registry",
                    "data" => $credentials
                ));

                break;
            case 'loginUser':
                $credentials = $JsonQuery["credentials"];
                
                $response = verifyPassword(array(
                    "table" => "usuario",
                    "correo" => $credentials["correo"],
                    "contrasena" => $credentials["contrasena"]
                ));

                if($response['result'] === 'success'){
                    $userId = handleSelection(array(
                        "table" => "usuario",
                        "select" => array("id"),
                        "where" => array(
                            "correo" => $credentials['correo']
                        )
                    ))[0]['id'];

                    $response['session'] = createUserSession($userId);
                    $response['userId'] = $userId;
                }

                break;
            case 'getTemplate':
                $id = $JsonQuery["organization"];
                
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
                $userType = $JsonQuery["userType"];
                $select = $JsonQuery["select"] ?? "";
                $where = $JsonQuery["where"] ?? "";
                $limit = $JsonQuery["limit"] ?? "";

                $data = handleSelection(array(
                    "table" => $userType,
                    "select" => $select,
                    "where" => $where,
                    "limit" => $limit
                ));

                $response = array(
                    "result" => "success",
                    "data" => $data
                );
                
                break;
            case 'verifyUserSession':

                $response = verifyUserSession($JsonQuery['session']);

                break;
            default:
                return errorResponse("La query no se encuentra entre los tipos de query validas ERROR-2");
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

