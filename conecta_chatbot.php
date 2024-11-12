<?php
//funciones/conecta.php chatbot
define("HOST",'localhost');
define("DB",'chatbot');
define("USER_DB",'root');
define("PASS_DB",'');

function conecta(){
    $con=new mysqli(HOST, USER_DB, PASS_DB, DB); //conexion a base de datos
    return $con;
}
?>