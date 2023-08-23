<?php

namespace Shibanashiqc\PhonePay;

use Shibanashiqc\PhonePay\Client;
use Shibanashiqc\PhonePay\Request; 

class PhonePay{    
    public $client;
    public $request;
    
    public function __construct($merchantId, $saltKey, $saltIndex) 
    {
        $this->client = new Client($merchantId, $saltKey, $saltIndex);
        $this->request = new Request();
    }
        
    /**
     * getPaymentRequest
     *
     * @param  mixed $amount
     * @param  mixed $merchantTransactionId
     * @param  mixed $merchantUserId
     * @param  mixed $mobileNumber
     */
    public function getPaymentRequest($amount, $merchantTransactionId = null, $merchantUserId = null, $mobileNumber = null) 
    {
        $data = [
            'merchantId' => $this->client->getMerchantId(),
            'merchantTransactionId' => $merchantTransactionId ?? uniqid(),
            'merchantUserId' => $merchantUserId ?? uniqid(),
            'amount' => $amount * 100,
            'redirectUrl' => $this->client->getRedirectUrl(),
            'redirectMode' => 'POST',
            'callbackUrl' => $this->client->getCallbackUrl(),
            'mobileNumber' => $mobileNumber ?? '9999999999',
            'paymentInstrument' => ['type' => 'PAY_PAGE',]
        ];
        
        $encode = base64_encode(json_encode($data));
        $signature = $this->client->generateSignature($encode, '/pg/v1/pay');
        $this->request->addHeader('X-VERIFY', $signature);
        $request = $this->request->request('/pg/v1/pay', ['request' => $encode]);
        return $request;
    }
        
    /**
     * getPaymentRedirectUrl
     *
     * @param  mixed $request
     * @return string
     */
    public function getPaymentRedirectUrl($request) : string
    {
        return isset($request['data']) ? $request['data']['instrumentResponse']['redirectInfo']['url'] : '';
    }
    
    
}