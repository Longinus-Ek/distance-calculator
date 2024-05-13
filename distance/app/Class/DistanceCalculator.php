<?php

namespace App\Class;

use App\Models\Cache;

class DistanceCalculator
{

    private $key;

    public function __construct($key)
    {
        $this->key =  array('Authorization: Token token=' . $key);
    }
    
    public function getDistance($latitude1, $latitude2, $longitude1, $longitude2)
    {
        $theta = $longitude1 - $longitude2; 
        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta))); 
        $distance = acos($distance); 
        $distance = rad2deg($distance); 
        $distance = $distance * 111.18957696; 

        $this->distance = (round($distance,2));

        return $this->distance;
    }

    function request($uri, $type_request) {
        sleep(1);
        if (!empty($uri)){
            try {
                $cache = new Cache();
                $request = curl_init();
                curl_setopt ($request, CURLOPT_HTTPHEADER, $this->key); // Access token for request.
                curl_setopt ($request, CURLOPT_URL, $uri); // Request URL.
                curl_setopt ($request, CURLOPT_RETURNTRANSFER, 1); 
                curl_setopt ($request, CURLOPT_CONNECTTIMEOUT, 5); // Connect time out.
                curl_setopt ($request, CURLOPT_CUSTOMREQUEST, $type_request); // HTPP Request Type.
                $file_contents = curl_exec($request);
                curl_close($request);
                $verify = json_decode($file_contents, true);
                if(count($verify) <= 0){
                    return 'Cep informado não existe!';
                }
                $cache->response = $file_contents;
                $cache->save();
                return json_decode($file_contents, true);

            } catch (Exception $e){
                return $e->getMessage();
            }
        }
    }

    function getCep($cepInfo){
        $type_request = "GET";
        $params = 'cep=' . $cepInfo;
        $uri = "https://www.cepaberto.com/api/v3/cep?". $params;
        
        if (!empty($params)){
            return $this->request($uri, $type_request);
        }
    }


}
    
