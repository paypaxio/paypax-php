<?php

namespace PayPaxPHP;

class Merchant
{
    private $apiKey = null;//string or null
    private $baseUrl='https://api.paypax.io/v1/merchant-api/v2';
    /**
     * Merchant constructor.
     * @param string $apiKey  your merchant API Key. more: https://app.paypax.io/en/merchant
     */
    public function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Create a PayPax order and get a payment link
     * @param string $amount numeric string with max 8 decimal points
     * @param string $callBackUrl payment callback url with GET method
     * @param string $orderId your internal order Id. this can be unique or not, based on your merchant setting at https://app.paypax.io/en/merchant
     * @param string $description optional. description of current order or a note/memo
     * @param string $orderPagePrimaryLanguage optional primary language of the user for payment screen. this is defaulted for en
     * @return mixed object {
    "ok": true/false,
    "trackingId": "123456",
    "orderId": "PayPax order ID",
    "paymentUrl": "https://api.paypax.io/v1/merchant-api/v2/startPayment/PayPax order ID"
    }
     */
    public function createOrder($amount,$callBackUrl,$orderId,$description='',$orderPagePrimaryLanguage='en')
    {
        $RequestData = [
            'amount' => $amount,
            'callBackUrl' => $callBackUrl,
            'orderPagePrimaryLanguage' => $orderPagePrimaryLanguage,
            'orderId' =>$orderId,
            'description' => $description
        ];
        $ch = curl_init($this->baseUrl.'/createOrder/'.$this->apiKey);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($RequestData));
        $response = curl_exec($ch);
        curl_close($ch);
        // decode response
        $response = json_decode($response);

        return $response;
    }

    /**
     * verify if PayPax payment is done and is verified or not
     * @param string|null $paypaxOrderId optional. PayPax Order ID. you have received on createOrder method or incoming callback GET params
     * @return mixed {
    "ok": true/false,
    "orderId": "PayPax Order ID"
    }
     */
    public function verifyOrder($paypaxOrderId=null){
        //if no order id is given then get it from magic methods
        if(!$paypaxOrderId && isset($_GET['merchantOrderId']) && !empty($_GET['merchantOrderId'])){
            $paypaxOrderId=$_GET['merchantOrderId'];
        }
        if(!$paypaxOrderId)
            return json_decode(json_encode(['ok'=>true]));

        $ch = curl_init($this->baseUrl.'/confirm-payment/'.$this->apiKey.'/'.$paypaxOrderId);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        // decode response
        $response = json_decode($response);

        return $response;
    }
}