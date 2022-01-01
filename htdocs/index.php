<?php
require __DIR__ . "/../vendor/autoload.php";

if(file_exists(__DIR__ . "/../app/vendor/autoload.php"))
    require __DIR__ . "/../app/vendor/autoload.php";

define("WEBENTRY", true);
chdir(__DIR__ . "/../app");
require("../binah/binah.php");
