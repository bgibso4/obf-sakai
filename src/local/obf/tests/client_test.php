<?php

/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 6/8/2017
 * Time: 1:10 PM
 */
class client_test
{
    public function checkingAuthentication(){
        include ('../class/client.php');
        $test= new client();
        print("hey");

        $apikey="RGONMMjDsmniKhSJ4HvRcG8GrAGfqaE0DCtgyXKyc77OR21Qy/yhYVfBdCDGFP5HppmlkiTGHOio
igU8G1l8p3U3chhf+dp7tkwXtFK7npZTiDx7Lu4FU1jtfab55+cexKKK77omVAsBpvnOjISH9UBk
UtKpXMtkb4zyrgEVtOA8LqWP93SAAu+c4/UANQCZ5rhEdVYcgs/sMMjAYNpTh70BWVcIYxFZVGNI
5CQjy1x2hiKuPgKKgP3VfONutHN9sgtnmmnY+uYETE26Wx3D4QXiUcxismfrB93SRFdvUxumAgMB
QX9mfpPodT5Mw7yusfb7+aTbeuwwv6FJXuyPMw==
";

        $url= 'https://openbadgefactory.com/';
        $test->authenticate($apikey, $url);

        $test->ping();
    }
}