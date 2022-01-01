<?php declare(strict_types=1);
namespace Binah;
use Symfony\Component\Yaml\Yaml;
use Nette\Caching\Storages\FileStorage;
use Nette\Caching\Cache;

function unyaml(string $str): string
{
    return function_exists("yaml_parse") ? yaml_parse($str) : Yaml::parse($file);
}

function unyaml_file(string $file): array
{
    static $cache = NULL;
    if(!$cache) {
        $fs    = new FileStorage(BINAH_HOME . "/tmp/yaml");
        $cache = new Cache($fs);
    }
    
    $result = $cache->load($file);
    if($result)
        return $result;
    
    $result = function_exists("yaml_parse_file") ? yaml_parse_file($file) : Yaml::parseFile($file);
    
    $cache->save($file, $result, $result, [
        Cache::EXPIRE  => "1 day",
        Cache::SLIDING => TRUE,
        Cache::FILES   => $file,
    ]);
    
    return $result;
}

function yaml($object): string
{
    return function_exists("yaml_emit") ? yaml_emit($object) : Yaml::dump($object);
}
