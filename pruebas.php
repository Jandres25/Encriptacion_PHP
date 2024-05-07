<?php
// Queremos crear un hash de nuestra contraseña usando el algoritmo DEFAULT actual.
// Actualmente es BCRYPT, y producirá un resultado de 60 caracteres.
//
// Hay que tener en cuenta que DEFAULT puede cambiar con el tiempo, por lo que debería prepararse
// para permitir que el almacenamiento se amplíe a más de 60 caracteres (255 estaría bien).

$passwordform = '12345678';

echo md5($passwordform) . "<br>"; // Esto produce una cadena de 32 caracteres
// Es seguro pero no es resistente a ataques de fuerza bruta

echo sha1($passwordform) . "<br> "; // Esto produce una cadena de  40 caracteres
// Tampoco es resistente a ataques de fuerza bruta, pero es un poco mejor que MD5

// Crear un hash de contraseña utilizando el algoritmo BCRYPT
// La función password_hash() generará un hash resistente a la fuerza bruta.
// El segundo parámetro indica el tipo de algoritmo a usar; en este caso, BCRYPT
// El tercer parámetro indica la complejidad del hash
$passwordhash = password_hash($passwordform, PASSWORD_DEFAULT);

echo $passwordhash . '<br>';

if (password_verify($passwordform, $passwordhash)) {
    echo 'La contraseña es válida';
} else {
    echo 'La contraseña no es válida';
}
