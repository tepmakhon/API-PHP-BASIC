<?php
include 'config.php';
function authenticate(){
    $headers = getallheaders();
    if(!isset($headers['API_KEY']) || $headers['API_KEY'] !== API_KEY){
        header("HTTP/1.1 401 Unauthorized");
        echo json_encode(["message" => "Invalid API key"]);
        exit();
    }
   
}


?>