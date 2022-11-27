<?php

/*Este php es al que se accede cuando entras por primera vez a la página, crea un objeto de la clase app y llama a la funcion run()*/

require_once "App.php";
$app = new App;
$app->run();