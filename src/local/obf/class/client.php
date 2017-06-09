<?php

/**
 * Created by PhpStorm.
 * User: Ben
 * Date: 6/8/2017
 * Time: 12:59 PM
 */

class client
{
    private $clientId;

    /**
     * @var $client Static client
     */
    private static $client = null;
    /**
     * @var curl|null Transport. Curl.
     */
    private $transport = null;
    /**
     * @var int HTTP code for handling errors, such as deleted badges.
     */
    private $httpcode = null;

    public function get_client_id(){
        return $this->clientId;
    }



    /**
     * Tries to authenticate the plugin against OBF API.
     *
     * @param string $signature The request token from OBF.
     * @return boolean Returns true on success.
     * @throws Exception If something goes wrong.
     */
    public function authenticate($signature, $url){
        //require_once("C:/Users/Ben/Documents/LTIDevelopment/LTI-Tool-Provider-Library-PHP/src/OAuth/OAuthConsumer.php");

        $cert_password= "hershey";
        $token = base64_decode($signature);
        $ch = curl_init($url);
        $curlopts = $this->get_curl_options();


        curl_setopt_array($ch, $curlopts);
        $url = $this->url_checker($url);


        $apiurl = $this->api_url_maker($url);

        // We don't need these now, we haven't authenticated yet.
        //unset($curlopts['SSLCERT']);
        //unset($curlopts['SSLKEY']);


        $pubkey="-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4crPmH5Dk7l8Cdg10TLH
CEX3ogMw89OlAq2F45fqv1FkmX6WjYn0ocS0dOMDa/4+EL4nuAcHKEdHkDnR/aAF
HH6xiuWuQxuIpeNPKHaiYVKJ0EdT5uXlknYfwue8yERow0T+93mrEaY2/0z7iWd3
fqmSn6/kUUJfyMqqizGkYIW54bA1X793Rb8z7/cspEpjxoCADO1Cz+tkAFhdQVdl
H/stldtOrwvCBhoRf+ifO8WG0/emcyHLlY+MV1Wz+7KJa1SFIYaBaaWgzPo5ckCq
F5DFUFSNXHU56B+nWvbTnpMyELmog/8K/ZgHxM4/AhbjJ8VlCUYs6YnkqMlEySja
QwIDAQAB
-----END PUBLIC KEY-----";


        //getting the json object of the $key created
        $key = openssl_pkey_get_public($pubkey);
        $decrypted = '';
        openssl_public_decrypt( base64_decode($signature), $decrypted, $key, OPENSSL_PKCS1_PADDING );
        $json = json_decode($decrypted);


        //extracting the id from the json text
        $this->clientId= $json->id;

        $config = array('private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA);
        $privkey = openssl_pkey_new($config);
        openssl_pkey_export_to_file($privkey, $this->get_pkey_filename());

        $csrout = '';
        $dn = array('commonName' => $json->id);

        $csr = openssl_csr_new($dn, $privkey);

        // Export the CSR into string.
        openssl_csr_export($csr, $csrout);


        //Making HTTP POST Request
        $signature = trim($signature);
        $postdata = json_encode(array('signature' => $signature, 'request' => $csrout));
//        $js = json_decode($postdata);
//        $s = (array)$js;
//        print_r ( $s );


        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_URL, $apiurl . '/client/' . $this->get_client_id() . '/sign_request');
        //curl_setopt($ch, CURLOPT_SSLCERTPASSWD, $cert_password);
        curl_setopt($ch, CURLOPT_POST, 2);
        //curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANYSAFE);
        $cert = curl_exec($ch);
//        print curl_error($ch);
//        print("   ");

//        print_r($cert);

        $info = curl_getinfo($ch);
//        print_r($info['http_code']);
        //print_r("          .");
        //print_r($httpcode);


        // Everything's ok, store the certificate into a file for later use.
        file_put_contents($this->get_cert_filename(), $cert, FILE_APPEND);

        return true;

    }

    /**
     * Get absolute filename of certificate key-file.
     * @return string
     */
    private function get_pkey_filename()
    {
        return $this->get_pki_dir().'obf.key';
    }

    /**
     * Get absolute filename of certificate pem-file.
     * @return string
     */
    public function get_cert_filename() {
        return $this->get_pki_dir() . 'obf.pem';
    }

    /**
     * Get absolute path of certificate directory.
     * @return string
     */
    public function get_pki_dir(){
        return 'C:/Users/Ben/Documents/LTIDevelopment/csrSigning/local_obf/pki/';
    }


    private function url_checker($url)
    {
        if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
            $url = "https://" . $url;
        }
        if (!preg_match("/\/$/", $url)) {
            $url = $url . "/";
        }
        return $url;
    }

    private function get_curl_options()
    {
        return array(
            CURLOPT_POST           => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS     => array(
                'signature' => 'sig',
                'request' => 'some other data',
            ),
            CURLOPT_FOLLOWLOCATION    => false,
//            CURLOPT_SSL_VERIFYHOST    => 2,
//            CURLOPT_SSL_VERIFYPEER    => 1,
//            CURLOPT_SSLCERT           => $this->get_cert_filename(),
//            CURLOPT_SSLKEY            => $this->get_pkey_filename()
        );
    }

    /**
     * set v1 to end of url.
     * example: https://openbadgefactory.com/v1
     *
     * @param  $url
     * @return string '$url/v1'
     */
    private static function api_url_maker($url) {
        $version = "v1";
        return $url . $version;
    }

    /**
     * testing to see if the Certificate has been created correctly
     *
     */
    public function ping(){
        $client_Id= 'OPP4V4a2PUa54';
        $ch = curl_init();

        $options = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_HEADER         => false,
            CURLOPT_URL     => 'https://openbadgefactory.com/v1/ping/' . $client_Id,
            CURLOPT_SSLCERT => $this->get_cert_filename(),
            CURLOPT_SSLKEY  => $this->get_pkey_filename(),
        );

        curl_setopt_array($ch , $options);
        $result = curl_exec($ch);
        $info   = curl_getinfo($ch);

        print_r($info['http_code']);
        curl_close($ch);

        /*
        * Expected results:
        *   $info['http_code'] == 200
        *   $result == $client_id
        */
    }

    public function getBadge($badgeID){

    }
}