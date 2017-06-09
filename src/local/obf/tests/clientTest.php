<?php
/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 6/8/2017
 * Time: 1:18 PM
 */
include ('../class/client.php');
$test= new client();

$apikey="X3I3SQzO5mCZHiL3SPkzkVfLZYNcZlk+2sSm/9AkYTSHRYwi1C+nS5TlF2SVS1AgR6zG8j+kLSZF
qlHIoL379E9HGp8ag30vIaI5D9sShtKY46oKUN2NajGxliqa6BhZE8QKJss3b37Y0AQgT1UTCz13
dPcfbseooGAk3qPCzryeygQM0yu9W0fK++jhXe92bxNevu4xuITgy4pKx8aMUk7l770OlnDNXqjp
cK+UyqCiQf53/ThUFAdPLghwQJXlnMgIamu0z7dwqsKWgZU+PEg2tB7H1cIUsIiia5uxCAloEUOf
o12qqD1iQtDv8R1nEm/yP4bsjzZTYP34GDSp7A==
";

$url= 'https://openbadgefactory.com/';
$test->authenticate($apikey, $url);

$test->ping();