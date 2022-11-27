<?php

/*Esta php me sirve para mostrar la base de datos entera, y cada vez que se actualiza el home se vuelve a mostrar*/

$dsn ="mysql:dbname=agenda;host=db";
$user="root";
$clave="password";

$db = new PDO($dsn, $user, $clave);


$resultadop = $db->query("SELECT * FROM contactos WHERE tipo = 'persona';");
$resultadoe = $db->query("SELECT * FROM contactos WHERE tipo = 'empresa';");

if ($resultadop->rowCount() > 0 || $resultadoe -> rowCount() > 0) {
    $disp_agen = '';
    $disp_agen = $disp_agen.'<h1>Tus contactos: </h1><br>';

    if ($resultadop->rowCount() > 0) {
        $disp_agen = $disp_agen.'<h3>Personas:</h3>';
    
        foreach ($resultadop as $value) {
            $disp_agen = $disp_agen."Nombre: $value[1] $value[2] Direccion: $value[3] Telefono: $value[4] <br>".'  <a href="?method=elim&id1=' . $value[4] . '"> Borrar contacto</a>
            <a href="?method=actuCon&id1=' . $value[4] . '"> Actualizar Contacto</a><br><br>';
        }
        
        $disp_agen = $disp_agen.'<br><br>';
    }

    if ($resultadoe -> rowCount() > 0) {
        $disp_agen = $disp_agen.'<h3>Empresas:</h3>';

        foreach ($resultadoe as $value) {
            $disp_agen = $disp_agen."Nombre: $value[1] Direccion: $value[3] Telefono: $value[4] Correo: $value[5] <br>".'  <a href="?method=elim&id1=' . $value[4] . '"> Borrar contacto</a>
            <a href="?method=actuCon&id1=' . $value[4] . '"> Actualizar Contacto</a><br><br>';
        }
    }

    
}
else {
    $disp_agen = $disp_agen.'<br><h2>No hay contactos en tu agenda</h2>';
}

echo $disp_agen;