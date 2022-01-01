<?php declare(strict_types=1);
namespace Binah;
use FastRoute\{Dispatcher, RouteCollector};
use Tracy\Debugger;

define("BINAH", true);
define("BINAH_HOME", __DIR__ . "/..");
define("APP_HOME", __DIR__ . "/../app");

foreach(glob(__DIR__ . "/*.php") as $incl)
    require_once $incl;

define("BINAH_CONF", unyaml_file(BINAH_HOME . "/binah.yml")["binah"]);

if(BINAH_CONF["debug"])
    Debugger::enable();

$appConfig = unyaml_file(APP_HOME . "/app.yml")["app"];
define("APP_CONF", $appConfig["config"]);

if(isset($appConfig["include"]))
    foreach($appConfig["include"] as $incl)
        require_once APP_HOME . "/$incl.php";

$router = \FastRoute\cachedDispatcher(function(RouteCollector $r) use ($appConfig) {
    foreach($appConfig["routes"] as $route) {
        if(!$route["handler"])
            continue;
        
        $url    = BINAH_CONF["path"] . ($route["url"] ?? "/");
        $method = $route["method"] ?? "GET";
        $tpl    = $route["template"] ?? NULL;
        $hand   = $route["handler"];
        $yml    = !($route["disallowYml"] ?? false);
        
        if($yml)
            $r->addRoute($method, "$url.yml", "YAML,$hand");
        
        if(!is_null($tpl))
            $r->addRoute($method, $url, "HTML,$hand,$tpl");
    }
}, [
    "cacheFile" => BINAH_HOME . "/tmp/routes/route.db",
]);

$request    = getThisRequest();
$httpMethod = $_SERVER["REQUEST_METHOD"];
$uri        = $_SERVER["REQUEST_URI"];

if (false !== $pos = strpos($uri, '?')) {
    $uri = substr($uri, 0, $pos);
}
$uri = rawurldecode($uri);

$routeInfo = $router->dispatch($httpMethod, $uri);
switch($routeInfo[0]) {
    case Dispatcher::NOT_FOUND:
        binah("HTML," . $appConfig["errors"]["notFound"]["handler"] . "," . $appConfig["errors"]["notFound"]["template"], $request, (object) []);
        break;
        
    case Dispatcher::METHOD_NOT_ALLOWED:
        binah("HTML," . $appConfig["errors"]["notAllowed"]["handler"] . "," . $appConfig["errors"]["notAllowed"]["template"], $request, (object) ["methods" => $routeInfo[1]]);
        break;
        
    case Dispatcher::FOUND:
        try {
            binah($routeInfo[1], $request, (object) $routeInfo[2]);
        } catch(\Exception $e) {
            binah("HTML," . $appConfig["errors"]["error"]["handler"] . "," . $appConfig["errors"]["error"]["template"], $request, (object) ["error" => $e]);
        }
        break;
}
