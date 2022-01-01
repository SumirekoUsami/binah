<?php declare(strict_types=1);
namespace Binah;
use Nyholm\Psr7\ServerRequest;
use Latte\Engine;

function binah(string $descriptor, ServerRequest $request, object $vars): void
{
    $descriptor = explode(",", $descriptor);
    $result     = (require(APP_HOME . "/" . $descriptor[1] . ".php"))($request, $vars);
    switch($descriptor[0]) {
        case "YAML":
            header("Content-Type: text/yaml");
            echo yaml($result);
            break;
        
        default:
        case "HTML":
            $latte = new Engine;
            $latte->setTempDirectory(BINAH_HOME . "/tmp/templates");
            $latte->render(APP_HOME . "/templates/" . $descriptor[2] . ".latte", (array) $result);
            break;
    }
}
