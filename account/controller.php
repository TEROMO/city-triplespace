<?php
    $routeInfo[2]['page'] = (empty($routeInfo[2]['page'])) ? '/' : $routeInfo[2]['page'] ;

    $ch = curl_init('https://city.triplespace.ru/api/user?type=auth');
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B179 Safari/7534.48.3" );
    curl_setopt($ch, CURLOPT_COOKIE, "uid={$_COOKIE['uid']};authData={$_COOKIE['authData']}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);
    $checkJSON = curl_exec($ch);
	curl_close($ch);
	$check = json_decode($checkJSON, true);
    
    if($check['type'] == "success"){
        $siteVars['user'] = $check['user'];

        $ch = curl_init('https://city.triplespace.ru/api/events?type=category-list');
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B179 Safari/7534.48.3" );
        curl_setopt($ch, CURLOPT_COOKIE, "uid={$_COOKIE['uid']};authData={$_COOKIE['authData']}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $checkJSON = curl_exec($ch);
	    curl_close($ch);
        $siteVars['engine']['category'] = json_decode($checkJSON, true);
        
        $ch = curl_init('https://city.triplespace.ru/api/events?type=get');
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 5_1 like Mac OS X) AppleWebKit/534.46 (KHTML, like Gecko) Version/5.1 Mobile/9B179 Safari/7534.48.3" );
        curl_setopt($ch, CURLOPT_COOKIE, "uid={$_COOKIE['uid']};authData={$_COOKIE['authData']}");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $checkJSON = curl_exec($ch);
	    curl_close($ch);
        $siteVars['events'] = json_decode($checkJSON, true)['get'];
        $siteVars['events']['count'] = count($siteVars['events']);

	}

    if ($siteVars['user']['group']['id'] > 0) {
        switch ($routeInfo[2]['page']){
            case '/':
                $siteFile = 'index.html';
            break;
            case 'event':
                $siteVars['page']['name'] = $_GET['page'];
                $siteVars['page']['title'] = 'События';
                $siteFile = 'event.html';
            break;
            case 'exit':
                setcookie('authData', null, -1);
                setcookie('uid', null, -1);
                header('Location: /lk');
                exit();
            break;
            case 'chat':
                $siteFile = 'chat.html';
            break;
            case 'profile':
                $siteFile = 'chat.html';
            break;
            default:
                $siteFile = '404.html';
            break;
        }
    }else{
        switch ($routeInfo[2]['page']){
            case '/':
                $siteFile = 'login.html';
            break;
            case 'reg':
                $siteFile = 'register.html';
            break;
            default:
                $siteFile = '404.html';
            break;
        }
    }

    $loader = new \Twig\Loader\FilesystemLoader('account');
    $twig = new \Twig\Environment($loader);
    $template = $twig->load($siteFile);
    $siteVars = (empty($siteVars)) ? array() : $siteVars ;
    echo $template->render($siteVars);
?>