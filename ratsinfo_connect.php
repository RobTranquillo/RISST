<?php
/*
 * RIS Session Connecter by tranquillo
 *
 * Das Programm verbindet sich mit dem Sitzungskalender der Stadt
 * Dresden (Somacos Session RIS). Es kann dann eine URL aus dem
 * RIS ausgelesen werden
 *
 *
 * robtranquillo@gmx.de
 * twitter.com/robtranquillo
 * github.com/robtranquillo
 *
 * Version 1.0 / 03.09.2015
 *
 * Licence: public domain with BY (Attribution)
 *
 * dependencies:
 * - php5-curl
 *
 * usage:
 *  include_once('ratsinfo_connect.php');
 *  $ratsinfo_connector = new Ratsinfo();
 *  $ratsinfo_connector->get_url($url);
 *
 */


/*
 * abstrahiert die Benutzung des Ratsinfo
 */

 class Ratsinfo {

    private $id = false;
    private $url_content = false;

    public function __construct() {
    }

     // gets the url content with the sessionid
     public function get_url( $url = 'null' )
     {
        if($url=='null') return array('error' => 'no-url');

        $this->id = $this->get_sessionid( $url );

        // Create a stream
        $opts = array(
          'http'=>array(
            'method'=>"GET",
            'header'=>"Accept-language: en\r\n" .
                      "Cookie: PHPSESSID=$this->id\r\n"
          )
        );
        $context = stream_context_create($opts);
        $file = file_get_contents($url, false, $context);
        $file = file_get_contents($url, false, $context); ##muss 2mal sein! sonst lädt er das falsche
        $this->url_content = $file;
        return $file;
     }



    private function get_sessionid( $link )
    {
            $ch = curl_init($link);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            $result = curl_exec($ch);

            // get cookie
            // multi-cookie variant contributed by @Combuster in comments
            preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $result, $matches);
            $cookies = array();
            foreach($matches[1] as $item) {
                    parse_str($item, $cookie);
                    $cookies = array_merge($cookies, $cookie);
            }
            #print_r($cookies);
            return $cookies['PHPSESSID'];
    }
 }



?>
