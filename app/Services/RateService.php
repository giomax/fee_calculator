<?php 
namespace App\Services;

class RateService{

    /**
     * Get Exchange Rates from paysera
     *
     * @return array
     * 
     */
    public function getRates(){
        $data = file_get_contents("https://developers.paysera.com/tasks/api/currency-exchange-rates");
        if($data === false){
            return ['code'=>0,'message'=>'Error on Get Rates','data'=>[]];
        }
        return ['code'=>1, 'message'=>'Done','data'=>json_decode($data,true)];
    }

}