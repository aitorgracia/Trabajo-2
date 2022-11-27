<?php

class App
{

    public $dsn ="mysql:dbname=agenda;host=db";
    public $user="root";
    public $clave="password";
    public $db;

    /*En el constructor además de iniciar sesion, creamos la conexion a la base de datos.*/

    public function __construct()
    {
      
  
      session_start();
    
      
          try {
              $this->db = new PDO($this->dsn, $this->user, $this->clave);
              
          } catch (PDOException $e) {
              echo 'Falló la conexión: ' . $e->getMessage();
          }
          
      }

    /*Este metodo se utiliza para direccionar llamadas de metodos desde los form.*/

    public function run()
    {
        if (isset($_GET['method'])) {
        $method = $_GET['method'];
        } else {
        $method = 'login';
        }
    
        $this->$method();      
    }

    /*Esta función te redirecciona al home, es un metodo auxiliar de  la función de arriba*/

    public function home()
    {
        include('views/home.php');
    }

  /*Esta funcion sirve para logear al usuario y si el navegador tiene la sesion ya inciada, salta 
  el login y te lleva directamente al home.*/

    public function login()
    {
        if (isset($_SESSION['name'])) 
        {
        header('Location: ?method=home');
        return;
        }

        include('views/login.php');
    }

    /*Esta funcion comprueba que el usuario y la contraseña introducidos en el login estan en la base
    de datos, si lo están lo dejan pasar, si no, el usuario tendra que volver a introducir sus credenciales.*/

    public function auth()
    {
        if (isset($_POST['name']) && !empty($_POST['name'])) {

            $name = $_POST['name'];
            $password = $_POST['password'];
            $acred = false;

            $resultado = $this->ejecutarConsulta("SELECT password FROM credenciales WHERE usuario = '".$name."'; ");

            foreach ($resultado as  $value) {

                /*El método password_verify sirve para desencriptar la contraseña almacenada en la base de datos.*/

                if (password_verify($password, $value[0])) {
                    
                    $acred = true;

                }
            }

            /*Si el usuario si acredita como usuario almacenado, se crea su sesion con nombre y se deja acceder.*/

            if ($acred) {
                $_SESSION['name'] = $name;
                $this->ejecutarConsulta(
                    "CREATE TABLE IF NOT EXISTS contactos( 
                    Tipo   VARCHAR (10) NOT NULL,
                    Nombre   VARCHAR (20) NOT NULL,
                    Apellidos VARCHAR (40),
                    Direccion  VARCHAR (40)NOT NULL,
                    Telefono  VARCHAR (13) NOT NULL,
                    Email  VARCHAR (40)
                    );");
                header('Location: ?method=home');
                return;
            }
        } 
        header('Location: ?method=login');

    }

    /*Esta funcion recorre el xml con contactos a agregar con la ayuda de 
    xpath y los introduce en la base de datos mediante una sentencia preparada.*/

    public function cargarXmlEnBd()
    {

        $datos = simplexml_load_file("agenda.xml")->xpath("//contacto");     


        foreach ($datos as $fila) {
            
            $atributo = $fila->xpath("./@tipo"); 
            $nombre = $fila->nombre;
            $apellidos = $fila->apellidos;
            $direccion = $fila->direccion;
            $telefono = $fila->telefono;
            $email = $fila->email;

            if ($atributo[0] == 'persona') {
                $sql = $this->db-> prepare("INSERT INTO contactos (tipo,nombre,apellidos,direccion,telefono) VALUES (?,?,?,?,?)");

                $sql->bindParam(1,$atributo[0]);
                $sql->bindParam(2,$nombre);
                $sql->bindParam(3,$apellidos);
                $sql->bindParam(4,$direccion);
                $sql->bindParam(5,$telefono);
            }
            else {
                $sql = $this->db-> prepare("INSERT INTO contactos (tipo,nombre,direccion,telefono,email) VALUES (?,?,?,?,?)");

                $sql->bindParam(1,$atributo[0]);
                $sql->bindParam(2,$nombre);
                $sql->bindParam(3,$direccion);
                $sql->bindParam(4,$telefono);
                $sql->bindParam(5,$email);
            }

            $sql->execute();

        }

        header('Location: index.php?method=home');

    }

    /*Esta funcion sirve para mostrar un formulario para añadir un contacto según el tipo 
    de contacto que quieras almacenar en la base de datos.*/

    public function tipo_cont()
    {

        if (isset($_POST['tipo']) && !empty($_POST['tipo'])) {
            
            $form = '<form action="?method=insertar" method="post">
                            <label for="name">Nombre:  </label>
                            <input type="text" name="nombre">
                            <br>
                            <label for="direc">Direccion:  </label>
                            <input type="text" name="direccion">
                            <br>
                            <label for="tlf">Telefono:  </label>
                            <input type="number" name="telefono">
                            <br>
                            <label for="email">Email:  </label>
                            <input type="text" name="email">
                            <input type="submit" value="Registrar">
                         </form>';

            if ($_POST['tipo'] == 'persona') {
        
                $form = '<form action="?method=insertar" method="post">
                         <label for="name">Nombre:  </label>
                         <input type="text" name="nombre">
                         <br>
                         <label for="apel">Apellido:  </label>
                         <input type="text" name="apellidos">
                         <br>
                         <label for="direc">Direccion:  </label>
                         <input type="text" name="direccion">
                         <br>
                         <label for="tlf">Telefono:  </label>
                         <input type="number" name="telefono">
                         <input type="submit" value="Registrar">
                     </form>';

            }
            else if ($_POST['tipo'] == 'ocult') {
                $form = '';
            }
        }

        include "views/home.php";

    }

     /*Esta funcion la uso para insertar */

     public function insertar()
     {
         $tipo = '';
         $nombre = $_POST["nombre"];
         $apellidos = $_POST["apellidos"];
         $direccion = $_POST["direccion"];
         $telefono = $_POST["telefono"];
         $email = $_POST["email"];
 
 
 
         if (isset($_POST["apellidos"])) {
 
             $sql = $this->db-> prepare("INSERT INTO contactos (tipo,nombre,apellidos,direccion,telefono) VALUES (?,?,?,?,?)");
 
             $tipo = 'persona';
 
                 $sql->bindParam(1,$tipo);
                 $sql->bindParam(2,$nombre);
                 $sql->bindParam(3,$apellidos);
                 $sql->bindParam(4,$direccion);
                 $sql->bindParam(5,$telefono);
 
         }
         else {
 
 
 
             $sql = $this->db-> prepare("INSERT INTO contactos (tipo,nombre,direccion,telefono,email) VALUES (?,?,?,?,?)");
 
             $tipo = 'empresa';
 
             $sql->bindParam(1,$tipo);
             $sql->bindParam(2,$nombre);
             $sql->bindParam(3,$direccion);
             $sql->bindParam(4,$telefono);
             $sql->bindParam(5,$email);
         }
 
         $sql->execute();
 
         header('Location: index.php?method=home');
 
     }

    /*Esta funcion fuciona para mostrar un form u otro segun el tipo de contacto del numero al que quieres actualizar, 
    si tu numero es de una empresa, el formulario es de introducir una empresa, si es de una persona, de un persona. */

    public function actuCon()
    {
        
        $id = $_GET['id1'];

        $resultado = $this->ejecutarConsulta("SELECT * FROM contactos WHERE Telefono = '".$id."'; ");

        foreach ($resultado as $value) {
            $tipo = $value[0];
        }

        if ($tipo == 'persona') {
            $form_actu = '<form action="?method=actualizar&id1=' . $id . '"" method="post">
                <label for="">Nombre nuevo  </label>
                <input type="text" name="nombre">
                <br>
                <label for="">Apellidos nuevos  </label>
                <input type="text" name="apellidos">
                <br>
                <label for="">Direccion nueva  </label>
                <input type="text" name="direccion">
                <br>
                <label for="">Telefono nuevo  </label>
                <input type="text" name="telefono">
                <br>
                <input type="submit" value="actualizar">
            </form>';
        }

        else {
            $form_actu = '<form action="?method=actualizar" method="post">
                <label for="">Nombre nuevo  </label>
                <input type="text" name="nombre">
                <br>
                <label for="">Direccion nueva  </label>
                <input type="text" name="direccion">
                <br>
                <label for="">Telefono nuevo  </label>
                <input type="text" name="telefono">
                <br>
                <label for="">Email nuevo  </label>
                <input type="text" name="email">
                <br>
                <input type="submit" value="actualizar">
            </form>';
        }

        include "views/home.php";

    }

    /*Esta es la función a la que se llama en la forms generadas por el anterior método, en la que se actualiza un contacto según su número*/

    public function actualizar()
    {

        $id = $_GET["id1"];
        $nombre = $_POST["nombre"];
        $apellidos = $_POST["apellidos"];
        $direccion = $_POST["direccion"];
        $telefono = $_POST["telefono"];
        $email = $_POST["email"];

        if (isset($_POST["apellidos"])) {
            $sql = $this->db-> prepare("UPDATE contactos SET tipo = 'persona', nombre = '".$nombre."',
            apellidos = '".$apellidos."',direccion = '".$direccion."',telefono = '".$telefono."' WHERE telefono = '".$id."';");

        }
        else {
            $sql = $this->db-> prepare("UPDATE contactos SET tipo = 'empresa', nombre = '".$nombre."',
            direccion = '".$direccion."',telefono = '".$telefono."',email = '".$email."' WHERE telefono = '".$id."';");
        }

        $sql->execute();

        header('Location: index.php?method=home');

    }

    /*Esta función elimina una tupla de la base de datos según su número de telefono*/

    public function elim()
    {
        
        $id = $_GET["id1"];

        $this->ejecutarConsulta("DELETE FROM contactos WHERE Telefono = '".$id."'; ");

        header('Location: index.php?method=home');

    }

    /*Este metodo se encarga de buscar un contacto en la base de datos, según su tipo y según su número*/

    public function busCont()
    {
        
        $bus_cont = '';

        $telefono = $_POST['telefono'];

        $resultadop = $this->db->query("SELECT * FROM contactos WHERE tipo = 'persona' AND telefono = ".$telefono.";");
        $resultadoe = $this->db->query("SELECT * FROM contactos WHERE tipo = 'empresa' AND telefono = ".$telefono.";");



    if ($resultadop->rowCount() > 0 || $resultadoe -> rowCount() > 0) {
        $bus_cont = $bus_cont.'<h1>Resultado de la búsqueda: </h1><br>';

        if ($resultadop->rowCount() > 0) {
            $bus_cont = $bus_cont.'<h3>Personas:</h3>';
        
            foreach ($resultadop as $value) {
                $bus_cont = $bus_cont."Nombre: $value[1] $value[2] Direccion: $value[3] Telefono: $value[4] <br>".'  <a href="?method=elim&id1=' . $value[4] . '"> Borrar contacto</a>
                <a href="?method=actuCon&id1=' . $value[4] . '"> Actualizar Contacto</a><br><br>';
            }
            
            $bus_cont = $bus_cont.'<br><br>';
        }

        if ($resultadoe -> rowCount() > 0) {
            $bus_cont = $bus_cont.'<h3>Empresas:</h3>';

            foreach ($resultadoe as $value) {
                $bus_cont = $bus_cont."Nombre: $value[1] Direccion: $value[2] Telefono: $value[3] Correo: $value[4] <br>".'  <a href="?method=elim&id1=' . $value[4] . '"> Borrar contacto</a>
                <a href="?method=actuCon&id1=' . $value[4] . '"> Actualizar Contacto</a><br><br>';
            }
        }

    
    }
    else {
        $bus_cont = $bus_cont.'<br><h2>No hay contactos en tu agenda con ese teléfono</h2>';
    }


        include "views/home.php";

    }

    /*Este metodo se encarga de limitar como puede ser el archivo que se sube, la imagen que se enlazaria con cada contacto, limita
    el archivo que se sube, solo podrá ser png, jpg o pdf, con un tamaño máximo de 5mb */

    public function subArch()
    {

        $type= $_FILES["myfile"]['type'];
        if(isset($_POST["envio"])){
          if($_FILES["myfile"]['size'] < 5000000){
            if($type=='image/png'||$type=='image/jpg'||$type=='application/pdf'){
    
            
              $nametemp=$_FILES["myfile"]["tmp_name"];
              $destino = 'uploads/'.$_FILES["myfile"]["name"];
             


             if(!move_uploaded_file($nametemp,$destino))
             {

                $result = "Fallo en la subida del archivo, intentalo de nuevo";

             }
             
             
          } 
      }else{
       
      }
      include('views/home.php');
      }
    }

    /*Esta funcion es la que se encarga de cerrar sesion. Destruye la session, elimina la tabla contactos y te manda a la página de login*/

    public function close()
    {
        session_destroy();
        
        $this->ejecutarConsulta("DROP TABLE contactos; ");

        header('Location: index.php?method=login');
    }

    /*Esta función se encarga de borrar todo el contenido de la base de datos*/

    public function borrarTodos()
    {
        $this->ejecutarConsulta("TRUNCATE TABLE contactos; ");

        header('Location: index.php?method=home');
    }

    /*Esta funcion auxiliar me la he creado para que sea más grafico en el archivo
    la visualizacion de cuando ejecuto conmandos sql en la base de datos*/

    public function ejecutarConsulta($sql)
    {
        
        return $this->db->query($sql);

    }

}