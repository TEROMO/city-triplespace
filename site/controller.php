<?php
    $routeInfo[2]['page'] = (empty($routeInfo[2]['page'])) ? '/' : $routeInfo[2]['page'] ;
    switch ($routeInfo[2]['page']){
        case '/':
            $siteFile = 'index.html';
        break;
        default:
            $siteFile = '404.html';
        break;
    }

    $loader = new \Twig\Loader\FilesystemLoader('site');
    $twig = new \Twig\Environment($loader);
    $template = $twig->load($siteFile);
    $siteVars = (empty($siteVars)) ? array() : $siteVars ;
    echo $template->render($siteVars);
?>