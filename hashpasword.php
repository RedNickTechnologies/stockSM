<?php
$password = "admin123";
// Usa el algoritmo estándar actual de la industria (generalmente bCrypt o Argon2)
$hash = password_hash($password, PASSWORD_DEFAULT);

echo $hash;
?>