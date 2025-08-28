<?php
session_start();
if (!isset($_SESSION["id_usuario"])) {
    header("Location: login.html");
    exit();
}
print "funcionou";
// Aqui você pode adicionar o código para exibir o dashboard do aluno
