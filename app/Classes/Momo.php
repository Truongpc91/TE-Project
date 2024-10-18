<?php

namespace App\Classes;

class Momo
{
    public function __construct() {}

    public function payment($order)
    {
        $endpoint = "https://test-payment.momo.vn/gw_payment/transactionProcessor";

        $momoConfig = momoConfig();

        $partnerCode = $momoConfig['partnerCode'];
        $accessKey = $momoConfig['accessKey'];
        $secretKey = $momoConfig['secretKey'];
        $orderInfo = "Thanh toán qua MoMo";
        $amount = (string)($order->cart['cartTotal'] - $order->promotion['discount']);
        $orderId = $order->code;
        $returnUrl = write_url('return/momo');
        $notifyurl = write_url('return/ipn');
        // Lưu ý: link notifyUrl không phải là dạng localhost
        // $bankCode = "SML";

        $orderid = $order->code;
        $orderInfo = (!empty($order->description)) ? $order->description : 'Thanh toán đơn hàng #' . $order->code . ' qua MOMOPAY';
        $bankCode = "";
        $requestId = time() . "";
        $requestType = "payWithMoMoATM";
        $extraData = "";
        //before sign HMAC SHA256 signature
        $rawHashArr =  array(
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderid,
            'orderInfo' => $orderInfo,
            'bankCode' => $bankCode,
            'returnUrl' => $returnUrl,
            'notifyUrl' => $notifyurl,
            'extraData' => $extraData,
            'requestType' => $requestType
        );
        // echo $serectkey;die;
        $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&bankCode=" . $bankCode . "&amount=" . $amount . "&orderId=" . $orderid . "&orderInfo=" . $orderInfo . "&returnUrl=" . $returnUrl . "&notifyUrl=" . $notifyurl . "&extraData=" . $extraData . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data =  array(
            'partnerCode' => $partnerCode,
            'accessKey' => $accessKey,
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderid,
            'orderInfo' => $orderInfo,
            'returnUrl' => $returnUrl,
            'bankCode' => $bankCode,
            'notifyUrl' => $notifyurl,
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        );
        $result = execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // decode json
        $jsonResult['url'] = $jsonResult['payUrl'];
        return $jsonResult;
    }
}
