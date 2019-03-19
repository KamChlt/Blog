<?php

/** Vérifie si un fichier a bien été déplacé et  */
function uploadFile($postName, $folder)
{
    if ($_FILES[$postName]["error"] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES[$postName]["tmp_name"];
        // basename() peut empêcher les attaques de système de fichiers;
        // la validation/assainissement supplémentaire du nom de fichier peut être approprié
        $name = uniqid().'-'.basename($_FILES[$postName]["name"]);
        if(move_uploaded_file($tmp_name, UPLOADS_DIR.$folder.'/'.$name))
            return $name;
    }
    
    return false;
}

/** Vérifie si un fichier a bien été déplacé et  */
function delFile($file)
{
    if(file_exists($file) && !is_dir($file))
        unlink($file);
}