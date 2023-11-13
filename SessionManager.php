<?php
    require_once("QueriesHandler.php");

    function createUserSession($userId){
        $sessionId = bin2hex(random_bytes(32));
        $tooken = bin2hex(random_bytes(16));

        while (sessionExistInDatabase($sessionId, $tooken)) {
            $sessionId = bin2hex(random_bytes(32));
            $tooken = bin2hex(random_bytes(16));
        }

        $response = handleInsertion(array(
            "table" => "sessions",
            "data" => array(
                "sessionId" => $sessionId,
                "tooken" => $tooken,
                "userAgent" => $_SERVER['HTTP_USER_AGENT'],
                "userId" => $userId
            )
        ));

        if($response['result'] != 'success'){
            return $response;
        }

        return [
            "sessionId" => $sessionId,
            "tooken" => $tooken
        ];
    }

    function verifyUserSession($session){
        if(!sessionExistInDatabase($session['sessionId'], $session['tooken'])) return false;

        $result = handleSelection(array(
            "table" => "sessions",
            "select" => array("userAgent", "userId"),
            "where" => array(
                "sessionId" => $session['sessionId'],
                "tooken" => $session['tooken']
            )
        ));

        return(
            $session['sessionId'] === $result[0]['sessionId'] &&
            $session['tooken'] === $result[0]['tooken'] &&
            $_SERVER['HTTP_USER_AGENT'] === $result[0]['userAgent'] &&
            $session['userId'] === $result[0]['userId']
        )
    }

    function sessionExistInDatabase($sessionId, $tooken){
        $checkQuery = "SELECT COUNT(*) as count FROM sessions WHERE sessionId = UNHEX('$sessionId') OR tooken = UNHEX('$tooken')";
        $result = exeQuery($checkQuery);

        if (!$result) {
            return false;
        }

        $row = mysqli_fetch_assoc($result);

        return $row['count'] > 0;
    }

    function __getClientIp() {
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
?>
