<?php declare(strict_types=1);
use Nyholm\Psr7\ServerRequest;

return function(ServerRequest $request, object $params) {
    return [
        "time" => time(),
    ];
};
