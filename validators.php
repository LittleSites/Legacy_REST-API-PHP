<?php
    function validateAuthData($data, $type){
        foreach ($data as $key => $value){
            if(!isset($key => $value)) return false;
        }

        return true
    }

?>