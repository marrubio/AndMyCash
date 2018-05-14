<?php
/**
 *
 * @About:      Database connection manager class
 * @File:       Database.php
 * @Date:       $Date:$ ABR-2018
 * @Version:    $Rev:$ 1.0
 * @Developer:  Mario Rubio (marugi@gmail.com)
 **/
require '../libs/vo/CuentaVO.php';
require '../libs/vo/PersonaVO.php';
require '../libs/vo/ApunteVO.php';

class DbHandler {
 
    private $conn;
 
    function __construct() {
		require_once dirname(__FILE__) . '/DbConnect.php';
        // opening db connection
        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    public function ejecutarConsulta($consulta)
    {    

        $rows = array();
        foreach($this->conn->query($consulta,PDO::FETCH_ASSOC) as $fila) {
            $rows[] = $fila;            
        }

        return $rows;
    }   
 
    //CUENTAS
    public function cargarCuentas()
    {

        $query = "SELECT * FROM MYC_CUENTA ORDER BY ID;";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    }   
    public function cargarCuenta($id)
    {

        $query = "SELECT * FROM MYC_CUENTA WHERE ID=".$id." ORDER BY ID;";
        $retArray = $this->ejecutarConsulta($query);        
        return $retArray;
    }
    public function insertarCuenta($cuenta)
    {        
     
        $insertQuery = "INSERT INTO MYC_CUENTA (categoria_fk,nombre,comentario) VALUES (".$cuenta->getCategoriaFk().",'".$cuenta->getNombre()."','".$cuenta->getComentario()."')";
        $stmt = $this->conn->prepare($insertQuery);


        return $stmt->execute();
    }
    public function borrarCuenta($id)
    {
        $query = "DELETE FROM MYC_CUENTA WHERE ID=".$id." ORDER BY ID;";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    }
    public function modificarCuenta($cuenta)
    {

        $query = "UPDATE MYC_CUENTA SET nombre='".$cuenta->getNombre()."' , comentario='".$cuenta->getComentario()."' , categoria_fk=".$cuenta->getCategoriaFk()." WHERE ID=".$cuenta->getId().";";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    }       

    //PERSONAS
    public function cargarPersonas()
    {
        $query = "SELECT * FROM MYC_PERSONA ORDER BY ID;";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    }  

    public function cargarPersona($id)
    {
        $query = "SELECT * FROM MYC_PERSONA WHERE ID=".$id." ORDER BY ID;";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;        
    }

    public function insertarPersona($persona)
    {                 
        $insertQuery = "INSERT INTO MYC_PERSONA (nombre,comentario,tipo_fk,direccion,poblacion,provincia,codigo_postal,pais,telefono,email) ";
        $insertQuery .= "VALUES ('".$persona->getNombre()."','".$persona->getComentario()."',".$persona->getTipoFk();
        $insertQuery .= ",'".$persona->getDireccion()."','".$persona->getPoblacion()."','".$persona->getProvincia()."'";
        $insertQuery .= ",'".$persona->getCodigoPostal()."','".$persona->getPais()."','".$persona->getTelefono()."'";        
        $insertQuery .= ",'".$persona->getEmail()."');";        

        $stmt = $this->conn->prepare($insertQuery);
        return $stmt->execute();
    }

    public function borrarPersona($id)
    {
        $query = "DELETE FROM MYC_PERSONA WHERE ID=".$id." ORDER BY ID;";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    }
    public function modificarPersona($persona)
    {

        $query = "UPDATE MYC_PERSONA SET ";
        $query .= "nombre='".$persona->getNombre()."' ";
        $query .= ",comentario='".$persona->getComentario()."'";        
        $query .= ",tipo_fk=".$persona->getTipoFk()." ";
        $query .= ",direccion='".$persona->getDireccion()."' ";
        $query .= ",poblacion='".$persona->getPoblacion()."' ";
        $query .= ",provincia='".$persona->getProvincia()."' ";
        $query .= ",codigo_postal='".$persona->getCodigoPostal()."' ";
        $query .= ",pais='".$persona->getPais()."' ";
        $query .= ",telefono='".$persona->getTelefono()."' ";
        $query .= ",email='".$persona->getEmail()."' ";
        $query .= "WHERE ID=".$persona->getId().";";
        
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    }

    //TIPOS DE PERSONA

    public function cargarTiposPersona()
    {

        $query = "SELECT * FROM MYC_TIPOPERSONA ORDER BY ID;";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    }  

    public function cargarTipoPersona($id)
    {
        $query = "SELECT * FROM MYC_TIPOPERSONA WHERE ID=".$id.";";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;        
    }
   
    //CATEGORIAS
    public function cargarCategorias()
    {

        $query = "SELECT * FROM MYC_CATEGORIA ORDER BY ID;";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    }  

    public function cargarCategoria($id)
    {

        $query = "SELECT * FROM MYC_CATEGORIA WHERE ID=".$id.";";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    } 

    //TIPOS CATEGORIAS
    public function cargarTiposCategoria()
    {

        $query = "SELECT * FROM MYC_TIPOCATEGORIA ORDER BY ID;";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    }
    public function cargarTipoCategoria($id)
    {

        $query = "SELECT * FROM MYC_TIPOCATEGORIA WHERE ID=".$id.";";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    } 
    //TIPOS APUNTE
    public function cargarTiposApunte()
    {

        $query = "SELECT * FROM MYC_TIPOAPUNTE ORDER BY ID;";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    } 

    public function cargarTipoApunte($id)
    {

        $query = "SELECT * FROM MYC_TIPOAPUNTE WHERE ID=".$id.";";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    } 
    
    //APUNTES
    public function insertarApunte($apunte)
    {        
    
    $insertApunte = "INSERT INTO MYC_APUNTE (FECHA,TIPO_FK,CUENTA_ORG_FK,CUENTA_DEST_FK,PERSONA_FK,COMENTARIO,IMPORTE,CONCILIADO,REFERENCIA_BNK) VALUES";
    $insertApunte .= " (STR_TO_DATE('".$apunte->getFecha()."','%d/%m/%Y'),".$apunte->getTipoFk().",".$apunte->getCuentaOrgFk().",".$apunte->getCuentaDestFk();
    $insertApunte .= ",".$apunte->getPersonaFk().",'".$apunte->getComentario()."','".$apunte->getImporte()."',".$apunte->getConciliado().
    $insertApunte .= ",'".$apunte->getReferenciaBnk()."');";        
    $stmt = $this->conn->prepare($insertApunte);

    return $stmt->execute();
    }               

    public function cargarApuntes()
    {

        $query = "SELECT * FROM MYC_APUNTE ORDER BY ID DESC LIMIT 20;";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    }  

    public function cargarApunte($id)
    {

        $query = "SELECT * FROM MYC_APUNTE WHERE ID=".$id.";";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    }  

    public function borrarApunte($id)
    {
        $query = "UPDATE FROM MYC_APUNTE SET BORRADO=1 WHERE ID=".$id." ORDER BY ID;";
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    }

    public function modificarApunte($persona)
    {

        $query = "UPDATE MYC_APUNTE SET ";
        $query .= "nombre='".$persona->getNombre()."' ";
        $query .= ",comentario='".$persona->getComentario()."'";        
        $query .= ",tipo_fk=".$persona->getTipoFk()." ";
        $query .= ",direccion='".$persona->getDireccion()."' ";
        $query .= ",poblacion='".$persona->getPoblacion()."' ";
        $query .= ",provincia='".$persona->getProvincia()."' ";
        $query .= ",codigo_postal='".$persona->getCodigoPostal()."' ";
        $query .= ",pais='".$persona->getPais()."' ";
        $query .= ",telefono='".$persona->getTelefono()."' ";
        $query .= ",email='".$persona->getEmail()."' ";
        $query .= "WHERE ID=".$persona->getId().";";
        
        $retArray = $this->ejecutarConsulta($query);
        return $retArray;
    }


}
?>