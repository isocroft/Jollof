<?php

/*!
 * Jollof Framework (c) 2016
 *
 *
 * {DBService.php}
 */

namespace Providers\Services;

use \Exception;
use \PDOException;
use \Providers\Core\DBConnection\BaseConnectionAdapter as BaseConnectionAdapter;
use \Providers\Core\QueryBuilder as Builder;

class DBService {

  /**
   * @var object (PDO/Mongo)
   */

	protected $connectionHandle = NULL;

  /**
   * @var array
   */

  protected $builders = array();

  /**
   * @var array
   */

	protected $param_types = array(
		"int" => PDO::PARAM_INT,
		"str" => PDO::PARAM_STR
  );

  /**
   * @var array
   */

	protected $config;

  /**
   * @var array
   */

  protected $mObjects = array();

  /**
   * @var bool
   */

	protected $isMSREnabled; // Master-Slave Replication - {Not In Use Now}

  /**
   * @var string
   */

  protected $db_engine;

  /**
   * @var BaseConnectionAdapter
   */

  protected $connectionAdapter;

  /**
   * @var array
   */

  protected $engineAdapterMap = array(
      'mysql' => '\Providers\Core\DBConnection\MySqlConnectionAdapter',
      'pgsql' => '\Providers\Core\DBConnection\PgSqlConnectionAdapter',
      'sqlite' => '\Providers\Core\DBConnection\SqlLiteConnectionAdapter',
      'mssql' => '\Providers\Core\DBConnection\MsSqlConnectionAdapter',
      'mongo' => '\Providers\Core\DBConnection\MongoConnectionAdapter'
  );

  /**
   * Constructor
   *
   *
   * @param array $config
   *
   */

  public function __construct(array $configs){


          $this->isMSREnabled = (bool) $configs['msr_enabled'];

          $this->db_engine = $configs['db_engine'];

          $engines = $configs['engines'];

          $this->config = $engines[$this->db_engine]; 

          switch($this->config['driver']){

          	   case "PDO":
               case "mongo":
                    $this->createConnectionAdapter($this->config['driver']);
          	   break;

               default:
                  return;
               break;

          }

    }

  /**
   * Destructor
   *
   *
   * @param void
   * 
   */

    public  function __destruct(){ # this will be used to disconnect from DB automatically

          if($this->hasConnection()){
          
                $this->disconnect();

          }       
    }

  /**
   * Clone
   *
   *
   * @param void
   * 
   */

    public function __clone(){


    }

  /**
   *
   *
   *
   *
   * @param array $models
   * @return void 
   */

    public function bindSchema($models){

        foreach($models as $model){

             $model->bindSchema();

        }
    } 

  /**
   *
   *
   *
   */

    protected function createConnectionAdapter($name){

        $adapter = ''; 

        if(isset($this->engineAdapterMap[$this->db_engine])){

              $adapter = $this->engineAdapterMap[$this->db_engine];

              $this->connectionAdapter = new $adapter($this->config['accessname'], ucfirst($name));
        }

    }   

  /**
   * Retrieves the query parameter types for a given PDO
   * connection which can either be an [integer] or a [string]
   *
   *
   * @param void
   * @return array $param_types 
   */

    protected function getParamTypes(){

    	   return $this->param_types;
    }

  /**
   * Establishes a valid database connection
   *
   *
   *
   * @param string $env_file
   * @return void 
   */

    protected function connect($env_file = ''){

         if($this->hasConnection()){

              /* do not try to connect to the DB if 
                we already have an active connection */
              return;  
         } 

         if(empty($env_file) || !isset($env_file)){

              return;

         }else{

              if(!file_exists($env_file)){

                  return;
              }
         }


    	   $settings = file($env_file);

         if(!is_array($settings)){

              return;
         }

         foreach ($settings as $line){
         	  
            $split = explode('=', $line);

            if(index_of($split[0], 'db_') === 0){

                $this->config[substr($split[0], 2)] = $split[1];
            }

         }

         try {


            $this->connectionHandle = $this->connectionAdapter->connect($this->config);

            #$this->connectionHandle->setAttribute(PDO::FETCH_CLASS);
            #$this->connectionHandle->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            #$this->connectionHandle->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

           // $this->config['engines']['mysql'] = $engine;

         }catch (Exception $e) { /* PDOException */

             throw $e;

         }
    }

  /**
   * Checks if a valid database connection has been made 
   *
   *
   *
   * @param void
   * @return bool 
   */

    protected function hasConnection(){

        return ($this->connectionHandle !== NULL);
    }

  /**
   * Retrieves the builder for a given [Model] object
   * using its' attributes [table-name, primary-key, relations]
   *
   *
   * @param array $modelAttributes
   * @return \Providers\Core\QueryBuilder $builder; 
   */

    public function getBuilder(array $modelAttributes, $modelName){

        $db_connection = $this->getConnection();

        $p_types = $this->getParamTypes();

        $table = (!array_key_exists('table', $modelAttributes))?: $modelAttributes['table'];

        if(empty($table) || !isset($table)){

              return NULL;
        }

        if(is_null($db_collection)){

              throw new Exception("No Database Connection Found, .env File Probably Missing");

        }

        $builder = $this->builders[$table] = new Builder($db_collection, $p_types, $modelName);

        $builder->setAttributes($modelAttributes);

        return $builder;
    }

  /**
   * Manually destroys the database connection object
   * and the cached builder objects
   *
   *
   * @param void
   * @return void 
   */

    protected function disconnect(){

          $this->builders = array();

          unset($this->connectionHandle);

          $this->connectionHandle = NULL;
    }

  /**
   *
   *
   *
   *
   * @param void
   * @return string
   */

  public function getDBEngine(){

      return $this->db_engine;
  }   

  /**
   * Retrieves the database connection object from memory
   *
   *
   *
   * @param void
   * @return array $connectionHandle 
   */

    protected function getConnection(){

          return $this->connectionHandle;
    }

}

?>