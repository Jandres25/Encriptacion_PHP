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

echo '<br>';

function cargarVariablesEntorno($ruta)
{
    if (!file_exists($ruta)) {
        return false;
    }

    $archivo = fopen($ruta, 'r');
    if ($archivo) {
        while (($linea = fgets($archivo)) !== false) {
            $linea = trim($linea);
            if (empty($linea) || strpos($linea, '#') === 0) {
                continue; // Ignorar líneas vacías y comentarios
            }

            list($nombre, $valor) = explode('=', $linea, 2);
            $nombre = trim($nombre);
            $valor = trim($valor);

            if (!array_key_exists($nombre, $_SERVER) && !array_key_exists($nombre, $_ENV)) {
                putenv(sprintf('%s=%s', $nombre, $valor));
                $_ENV[$nombre] = $valor;
                $_SERVER[$nombre] = $valor;
            }
        }
        fclose($archivo);
        return true;
    }

    return false;
}

$rutaArchivoEnv = __DIR__ . '/.env'; // Ajusta la ruta si es necesario
cargarVariablesEntorno($rutaArchivoEnv);

// Ahora puedes acceder a las variables de entorno
$smtpHost = $_ENV["SMTP_HOST"];
$smtpUsername = $_ENV["SMTP_USERNAME"];
$smtpPassword = $_ENV["SMTP_PASSWORD"];
$smtpPort = $_ENV["SMTP_PORT"];

echo "Host: " . $smtpHost . PHP_EOL;
echo "Username: " . $smtpUsername . PHP_EOL;
echo "Password: " . $smtpPassword . PHP_EOL;
echo "Port: " . $smtpPort . PHP_EOL;
