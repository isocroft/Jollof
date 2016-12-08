<?php

/*!------------------------------------------------
 ! Class Loader for Jollof
 !
 !
 !
 !
 !* Jollof (c) Copyright 2016
 !*
 !*
 !* {ClassLoader.php}
 !*
 !*
 -------------------------------------------------*/



class ClassLoader {

    private $classMapSuper;

    private $classMap;

    private $options;

    private $registered = false;

    const ROOT = __DIR__; // dirname(dirname(__FILE__));


    public function __construct(array $options){

        $this->options = $options;
        
        $this->classMapSuper = true;

        $this->classMap = array();

    }

    /**
     *
     *
     * @param array $maps
     * @return void
     */

     public function addClassMap($maps)
     {

         foreach($maps as $key => $val){

              if(array_key_exists($key, $this->classMap)){
                
                    continue;

              }

              $this->classMap[$key] = $val;

         }

     }

    /**
     *
     *
     *
     * @param bool $splitNumber
     * @return mixed
     */

    private function getHostVersion($splitNumber){

         $version = phpversion();

         if($splitNumber){

              return explode('.', $version);

         }else{

              return $version;
         }  
    } 

    /**
     *
     *
     *
     * @param void
     * @return bool
     */

     public function isRegistered(){

         return $this->registered;
     } 

    /**
     *
     *
     *
     * @param void
     * @return array
     */

     public function getClassMap()
    {
        return $this->classMap;
    }

    /**
     * Should class lookup fail if not found in the current class map?
     *
     *
     * @param void
     * @return bool
     */
    public function isClassMapSuper()
    {
        return $this->classMapSuper;
    }

    /**
     * Registers this instance as an autoloader.
     *
     * @param bool $prepend [Whether to prepend the autoloader or not]
     * @return void
     */
    public function register($prepend = false)
    {
      if(function_exists('spl_autoload_register')){ // PHP 5.3+

           $this->registered = spl_autoload_register(array($this, 'loadClass'), true, $prepend);


        }else{ // PHP 5.0 - PHP 5.2

               $CLASSMAP = $this->getClassMap();
         
               require self::ROOT . '/_autoload.php';

        }
    }

    /**
     * Unregisters this instance as an autoloader.
     *
     *
     * @param void
     * @return void
     */
    public function unregister()
    {

      if(function_exists('spl_autoload_register')){

           spl_autoload_unregister(array($this, 'loadClass'));

           $this->registered = false;

      }else{

           ;
      }
    }

    /**
     * Loads the given class or interface.
     *
     * @param  string    $class The name of the class
     * @return mixed True if loaded, null otherwise
     */
    public function loadClass($class)
    {
        if (($file = $this->findFile($class)) !== NULL) {

            $this->includeFile($file);

            return true;
        }

        return false;
    }

    /**
     * Finds the path to the file where the class is defined.
     *
     * @param string $class The name of the class
     * @return string|false The path if found, false otherwise
     */
    public function findFile($class)
    {
        // for PHP 5.3.0 - 5.3.2, we need not add a leading backward slash
        if(!(substr($this->getHostVersion(false), 0, 3) === '5.3')){
            $class = "\\" . $class;
        }   
        // class map lookup
        if (isset($this->classMap[$class])) {
            return $this->classMap[$class];
        }
        if ($this->classMapSuper) {
            return NULL;
        }

        return NULL;
    }

    private function includeFile($file){
     
         include str_replace(basename(self::ROOT), '', self::ROOT) . $file . ".php";
      
    }

 }   

?>
