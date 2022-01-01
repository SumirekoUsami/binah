<?php declare(strict_types=1);
namespace Binah;
use Nyholm\Psr7\{ServerRequest, Factory\Psr17Factory};
use Nyholm\Psr7Server\ServerRequestCreator;

function getThisRequest(): ServerRequest
{
    $psr17Factory = new Psr17Factory;
    $creator      = new ServerRequestCreator(
        $psr17Factory,
        $psr17Factory,
        $psr17Factory,
        $psr17Factory
    );
    
    return $creator->fromGlobals();
}
