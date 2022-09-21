<?php 
namespace App\Services;
use App\Services\RateService;

class FeeService{

    private $allRates;
    private $roundDigit = 2;
    private $depositFee = 0.0003;
    private $privateWithdrawFee = 0.003; 
    private $privateWidhdrawFreeLimitWeek = 1000;
    private $businessWidhdrawfee = 0.005;
    private $freeLimitPerWeek = 3;


    public function calculate($file){
        $this->allRates = (new RateService())->getRates();
        if(!$this->allRates['code']){
            return $allRates;
        }

        $privateUsersAlreadyWithdrawed = [];
        $data = [];
     
        while (($row = fgetcsv($file)) !== false) {
            $date = $row[0];
            $userId = $row[1];
            $userType = $row[2];
            $transType = $row[3];
            $currency = $row[5];
            $amount = $this->convertEuro($row[4],$currency);            
            
            if($transType == 'deposit'){
             //DEPOSIT TRANSACTIONS   
             $fee = $this->calc($amount,$this->depositFee);
            }else{
            //WITHDRAW TRANSACTIONS
                if($userType == 'private'){
                    //FILTER WITH USER TYPE PRIVATE
                    if(isset($privateUsersAlreadyWithdrawed[$userId])){
                        //CHECK TOTAL AMOUNT AND COUNT
                        $fee = 0;
                        if($privateUsersAlreadyWithdrawed[$userId]['date'] == date("Wo",strtotime($date))){
                            //user has already withdrawed on a same week
                            if($privateUsersAlreadyWithdrawed[$userId]['count'] < $this->freeLimitPerWeek){
                                //user has no limit yet with count
                                if($privateUsersAlreadyWithdrawed[$userId]['alreadyWithdrawed'] < $this->privateWidhdrawFreeLimitWeek){
                                    //can withdraw some more money with free fee
                                    $privateUsersAlreadyWithdrawed[$userId]['alreadyWithdrawed'] += $amount;
                                    if($privateUsersAlreadyWithdrawed[$userId]['alreadyWithdrawed'] > $this->privateWidhdrawFreeLimitWeek){
                                        //limited
                                        $fee = $this->calc($privateUsersAlreadyWithdrawed[$userId]['alreadyWithdrawed']-$this->privateWidhdrawFreeLimitWeek,$this->privateWithdrawFee);
                                    }else{
                                        //free
                                        $fee = 0;
                                    }     
                                    $privateUsersAlreadyWithdrawed[$userId] = ['count'=>$privateUsersAlreadyWithdrawed[$userId]['count']+1,'date'=>date("Wo",strtotime($date)),'alreadyWithdrawed'=>$privateUsersAlreadyWithdrawed[$userId]['alreadyWithdrawed']];                               
                                }else{
                                    //already limited with withdrawed amount
                                    $fee = $this->calc($amount,$this->privateWithdrawFee);
                                }
                            }else{
                                //user has limit with count
                                $fee = $this->calc($amount,$this->privateWithdrawFee);
                            }  
                        }else{
                            //started Next week need clear user
                            unset($privateUsersAlreadyWithdrawed[$userId]);
                        }
                    }
                    if(!isset($privateUsersAlreadyWithdrawed[$userId])){
                        //WITH free fee
                        if($amount >= $this->privateWidhdrawFreeLimitWeek){
                            //1000 is free
                            $amount -= $this->privateWidhdrawFreeLimitWeek;
                            $alreadyWithdrawed = $this->privateWidhdrawFreeLimitWeek;
                            $fee = $this->calc($amount,$this->privateWithdrawFee);
                        }else{
                            $alreadyWithdrawed = $amount;
                            $fee = 0;
                        }                        
                        $privateUsersAlreadyWithdrawed[$userId] = ['count'=>1,'date'=>date("Wo",strtotime($date)),'alreadyWithdrawed'=>$alreadyWithdrawed];
                        
                    }
                }else{
                    //FILTER WITH USER TYPE BUSINESS
                    $fee = $this->calc($amount,$this->businessWidhdrawfee);
                }
            }
            
            $data[] = $this->convertFee($fee,$currency);
        }
        return ['code'=>1, 'message'=>'Done', 'data'=>$data,'privateUsersAlreadyWithdrawed'=>$privateUsersAlreadyWithdrawed];
    }

    private function calc($amount,$fee){
        return number_format($amount*$fee,$this->roundDigit);
    }

    
    /**
     * Convert Currency to EURO
     *
     * @param mixed $amount
     * @param mixed $fromCurrency
     * 
     * @return float
     * 
     */
    public function convertEuro($amount,$fromCurrency){
        if($fromCurrency == 'EUR'){
            return $amount;
        }
        return $amount / $this->allRates['data']['rates'][$fromCurrency];
    }

    /**
     * Convert Eur Fee to initial currency
     *
     * @param mixed $amount
     * @param mixed $toCurrency
     * 
     * @return float
     * 
     */
    public function convertFee($amount,$toCurrency){
        if($toCurrency == 'EUR'){
            return number_format($amount,$this->roundDigit);
        }
        return number_format($amount * $this->allRates['data']['rates'][$toCurrency],$this->roundDigit);
    }
}