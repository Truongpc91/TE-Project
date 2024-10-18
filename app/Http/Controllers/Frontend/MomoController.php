<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\OrderReponsitoryInterface as OrderReponsitory;
use App\Services\Interfaces\OrderServiceInterface as OrderService;

class MomoController extends Controller
{
    protected $OrderReponsitory;
    protected $OrderService;

    public function __construct(
        OrderReponsitory $OrderReponsitory,
        OrderService $OrderService,
    ) {
        $this->OrderReponsitory = $OrderReponsitory;
        $this->OrderService = $OrderService;
    }

    public function momo_return(Request $request)
    {
        $template = 'frontend.cart.success';
        $config = $this->config();
        $momoConfig = momoConfig();
        $secretKey = $momoConfig['secretKey']; //Put your secret key in there

        if (!empty($_GET)) {
            $partnerCode = $_GET["partnerCode"];
            $accessKey = $_GET["accessKey"];
            $orderId = $_GET["orderId"];
            $localMessage = utf8_encode($_GET["localMessage"]);
            $message = $_GET["message"];
            $transId = $_GET["transId"];
            $orderInfo = utf8_encode($_GET["orderInfo"]);
            $amount = $_GET["amount"];
            $errorCode = $_GET["errorCode"];
            $responseTime = $_GET["responseTime"];
            $requestId = $_GET["requestId"];
            $extraData = $_GET["extraData"];
            $payType = $_GET["payType"];
            $orderType = $_GET["orderType"];
            $extraData = $_GET["extraData"];
            $m2signature = $_GET["signature"]; //MoMo signature


            //Checksum
            $rawHash = "partnerCode=" . $partnerCode . "&accessKey=" . $accessKey . "&requestId=" . $requestId . "&amount=" . $amount . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo .
                "&orderType=" . $orderType . "&transId=" . $transId . "&message=" . $message . "&localMessage=" . $localMessage . "&responseTime=" . $responseTime . "&errorCode=" . $errorCode .
                "&payType=" . $payType . "&extraData=" . $extraData;

            $partnerSignature = hash_hmac("sha256", $rawHash, $secretKey);


            echo "<script>console.log('Debug huhu Objects: " . $rawHash . "' );</script>";
            echo "<script>console.log('Debug huhu Objects: " . $secretKey . "' );</script>";
            echo "<script>console.log('Debug huhu Objects: " . $partnerSignature . "' );</script>";
            $order = $this->OrderReponsitory->findByCondition([
                ['code', '=', $orderId]
            ], FALSE, ['products']);
            $templatePayment = 'frontend.cart.components.momo';
            return view($template, compact(
                'config',
                'order',
                'templatePayment',
                'm2signature',
                'partnerSignature',
                'message',
                'localMessage'
            ));
            // if ($m2signature == $partnerSignature) {
            //     if ($errorCode == '0') {
            //         $result = '<div class="alert alert-success"><strong>Payment status: </strong>Success</div>';
            //     } else {
            //         $result = '<div class="alert alert-danger"><strong>Payment status: </strong>' . $message . '/' . $localMessage . '</div>';
            //     }
            // } else {
            //     $result = '<div class="alert alert-danger">This transaction could be hacked, please check your signature and returned signature</div>';
            // }
        } else {
            abort(404);
        }
    }

    private function config()
    {
        return [
            'css' => [
                'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css'
            ],
            'js' => [
                'backend/library/location.js',
                'frontend/core/library/product.js',
            ]
        ];
    }
}
