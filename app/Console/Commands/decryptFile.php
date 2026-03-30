<?php
use PHPCrypt\PHPBolt;

function decryptFile($encryptedPath)
{
    $key = env('ENCRYPTION_KEY');

    // Leer archivo encriptado
    $encrypted = file_get_contents($encryptedPath);

    $encrypted = preg_replace('/^\xEF\xBB\xBF/', '', $encrypted);

    // Eliminar espacios o saltos ANTES del <?php
    $encrypted = ltrim($encrypted);

    if (!str_starts_with($encrypted, '<?php')) {
        die("Invalid encrypted file header");
    }

    $bolt = new PHPBolt();

    // Desencriptar usando bolt_decrypt
    
    $decrypted = $bolt->bolt_decrypt($encrypted, $key);
    var_dump($decrypted === false ? 'FAIL' : 'OK');

    if ($decrypted === false || $decrypted === null || $decrypted === '') {
    die("bolt_decrypt failed — key mismatch or corrupted file");
}


    file_put_contents($encryptedPath, $decrypted);
}
?>