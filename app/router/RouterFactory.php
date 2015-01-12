<?php

use Nette\Application\Routers\RouteList;
use Nette\Application\Routers\Route;


/**
 * Router factory.
 */
class RouterFactory
{

  /**
   * @return \Nette\Application\IRouter
   */
  public function createRouter()
  {
    $router = new RouteList;

    $routerAdmin = new RouteList('Admin');
    $routerAdmin[] = new Route('admin/<presenter>/<action>[/<id>]', 'Home:default');
    $router[] = $routerAdmin;

    $routerFront = new RouteList('Front');
    $routerFront[] = new Route('<presenter>/<action>[/<id>]', 'Home:default');
    $router[] = $routerFront;

    return $router;
  }

}
