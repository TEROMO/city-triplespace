<?php 
	if($_GET['error'] == true){
		ini_set('error_reporting', E_ALL);
		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
	}

	require 'vendor/autoload.php';
	
	function removeBOM($str="") {
	    if(substr($str, 0, 3) == pack('CCC', 0xef, 0xbb, 0xbf)) {
	        $str = substr($str, 3);
	    }
	    return $str;
	}

	\RedBeanPHP\R::setup('mysql:host=127.0.0.1;dbname=j90487hq_main','j90487hq_main', 'c*G0d&Bs');
	if ( !\RedBeanPHP\R::testConnection() )
	{
		$loader = new \Twig\Loader\FilesystemLoader('engine');
	    $twig = new \Twig\Environment($loader);
	    $template = $twig->load('not_connect.html');
	    $siteVars = (empty($siteVars)) ? array() : $siteVars ;
        exit ($template->render($siteVars));
	}

	$siteVars = json_decode(removeBOM(file_get_contents('site.json')), true);

	\RedBeanPHP\R::ext('xdispense', function($table_name){
		return \RedBeanPHP\R::getRedBean()->dispense($table_name);
	});

	$dispatcher = FastRoute\simpleDispatcher(function(FastRoute\RouteCollector $r) {
	    $r->addRoute('GET', '/', array('type' => 'site', 'file' => 'controller.php'));
	    $r->addRoute('GET', '/lk', array('type' => 'account', 'file' => 'controller.php'));
	    $r->addRoute('GET', '/lk/{page}', array('type' => 'account', 'file' => 'controller.php'));
	    $r->addRoute(['GET', 'POST'], '/api/{page}', array('type' => 'engine/api', 'file' => 'controller.php'));
	});

	$httpMethod = $_SERVER['REQUEST_METHOD'];
	$uri = $_SERVER['REQUEST_URI'];

	if (false !== $pos = strpos($uri, '?')) {
	    $uri = substr($uri, 0, $pos);
	}
	$uri = rawurldecode($uri);

	$routeInfo = $dispatcher->dispatch($httpMethod, $uri);
	switch ($routeInfo[0]) {
	    case FastRoute\Dispatcher::NOT_FOUND:
	        http_response_code(404);
	        break;
	    case FastRoute\Dispatcher::METHOD_NOT_ALLOWED:
	        $allowedMethods = $routeInfo[1];
	        http_response_code(405);
	        break;
		case FastRoute\Dispatcher::FOUND:
			try {
				require $routeInfo[1]['type'].'/'.$routeInfo[1]['file'];
			} catch (\Throwable $th) {
				http_response_code(404);
			}
	        break;
	}
?>