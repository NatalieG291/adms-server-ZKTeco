<?php

function encryptFile($inputPath, $outputPath)
{
    $key = env('ENCRYPTION_KEY');

    // Leer código original
    $content = file_get_contents($inputPath);
    $pattern = '/\<\?php/m';
    preg_match($pattern, $content, $matches);
    if (!empty($matches[0])) {
        $content = preg_replace($pattern, '', $content);
    }

    // Encriptar
    $encrypted = bolt_encrypt($content, $key);

    // Crear archivo auto-descriptable
    $final = "<?php
bolt_decrypt(__FILE__, env('ENCRYPTION_KEY')); return 0;
##!!!##" . $encrypted;

    // Guardar en cualquier ruta
    file_put_contents($outputPath, $final);
}
?>