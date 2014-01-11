<?php

/**
 * Controller class, comes from the Origin Framework
 */
class Origin_Controller {
	/**
	 * Config loaded from the config file
	 */
	protected $config;
	
	/**
	 * Request argument that'll trigger methods
	 */
	protected $method_arg;
	
	/**
	 * @var Singleton instance
	 */
	protected static $instances;
	
	function __construct($config = true, $method_arg = 'method'){
		if($config === true) $config = dirname(__FILE__).'/config.php';
		
		if(!empty($config)){
			$config_array = include($config);
			$this->config = (object) $config_array;
		}
		
		$this->method_arg = $method_arg;
		
		$methods = get_class_methods($this);
		foreach($methods as $method){
			switch(substr($method,0,6)) {
				case 'action' : add_action(substr($method,7), array($this, $method)); break;
				case 'filter' : add_filter(substr($method,7), array($this, $method)); break;
			}
		}
		
		add_action('template_redirect', array($this, 'method_handler'));
	}
	
	/**
	 * Get the singleton of this controller
	 */
	public static function single($class)
    {
		if(empty($class)) $class = __CLASS__;
		if(!isset(self::$instances)) self::$instances = array();
		
        if (!isset(self::$instances[$class])) {
			// Create and initialize the singleton
            self::$instances[$class] = new $class();
			self::$instances[$class]->init();
        }
		
        return self::$instances[$class];
    }
	
	/**
	 * Initialize the Method
	 */
	function method_handler(){
		global $wp_query;
		// Get the method from either wp_query or the request array
		$method = $wp_query->get($this->method_arg);
		if(empty($method)) $method = @$_REQUEST[$this->method_arg];
		
		if(!empty($method)){
			$exit = false;
			if(!empty($this->methods[$method])){
				$exit = call_user_func($this->methods[$method]);
			}
			
			$method_handler = 'method_'.$method;
			if(method_exists($this, $method_handler)){
				$exit = $this->{$method_handler}();
			}
			
			if($exit === true){
				do_action('wp_shutdown');
				exit();
			}
		}
	}
	
	/**
	 * Adds a custom URL method handler
	 */
	function register_method($method, $handler){
		$this->methods[$method] = $handler;
	}
	
	public function get_config(){
		return $this->config;
	}
	
	function init(){
		
	}
}