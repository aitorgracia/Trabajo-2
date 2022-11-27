<?php

  session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
  <title>Document</title>
</head>
<body>

    <?php require_once "header2.html"?>

        <div class="general">

        <!-- Da la bienvenida al usuario usando el nombre asignado a la sesion -->

            <h1>Bienvenido <? echo $_SESSION['name']; ?>:</h1>

            <div class="cont_pag">

                <div class="izq">

                <h1>Insertar un contacto: </h1>

                    <h2>Tipo de contacto</h2>
                    <form action="?method=tipo_cont" method="post" id="TipoCont">
                        <label for="one">Persona</label>
                        <input type="radio" name="tipo" id="one" value="persona" checked>
                        <label for="two">Empresa</label>
                        <input type="radio" name="tipo" id="two" value="empresa" >
                        <label for="three">Ocultar formulario</label>
                        <input type="radio" name="tipo" id="three" value="ocult">
                        <br>
                        <input type="submit" value="Enviar">
                        <br>
                    </form>

                <?php echo $form?>                

                <br><br>

                <!-- Muestra toda la agenda de contactos -->
                
                <?php include ("impAgen.php"); ?>

                <br><br>
                </div>
                
                <div class="centro">
                
                <!-- Este fragmento de php se usa para mostrar el formulario de actulizar un contacto -->

                <?php if (isset($resultado) && !empty($resultado)) {

                        echo "<h1>Actualizar el contacto cuyo numero es $id: </h1><br><br>";

                        echo $form_actu;
                }
                ?>

                <br><br>

                <h1>Subir foto: </h1>

                <form action="?method=subArch" method="post" enctype="multipart/form-data">
                    
                    <label for="mifich" >Seleciona un fichero </label>
                    <input type="file" name="myfile" id="mifich">
                    <input type="submit" name= "envio" value="Enviar Fichero">
                    
                </form>

                <!-- Muestra si ha habido algún error enla subida del archivo, si ha ido todo bien no muestra nada -->

                <?php echo $result; ?>

                </div>

                <div class="derecha">

                <h1>Mostrar contacto: </h1>
                <br><br>

                <form action="?method=busCont" method="post">
                        <label for="one">Numero: </label>
                        <input type="number" name="telefono" id="one">
                        <br>
                        <input type="submit" value="Buscar">
                        <br>
                    </form>
                <br>

                    <!-- muestra el resultado de la busqueda de contacto -->

                    <?php echo $bus_cont; ?>

                </div>

            </div>

            </div>

            <?php require_once "footer.html"?>
    </body>
</html>