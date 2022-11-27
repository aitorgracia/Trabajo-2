<?php

  session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
  <title>Document</title>
</head>
<body>

    <?php require "header.html"?>

  <h1>Formulario de login</h1>

  <form action="?method=auth" method="post">
    <label for="">Nombre  </label>
    <input type="text" name="name">
    <br>
    <label for="">Password  </label>
    <input type="text" name="password">
    <br>
    <input type="submit" value="login">
  </form>

  <?php require "footer.html"?>

</body>
</html>