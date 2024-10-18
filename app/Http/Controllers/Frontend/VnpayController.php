<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Interfaces\OrderReponsitoryInterface as OrderReponsitory;
use App\Services\Interfaces\OrderServiceInterface as OrderService;
use Exception;

class VnpayController extends Controller
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

    public function vnpay_return(Request $request)
    {
        $orderCode = $request->input('vnp_TxnRef');
        $order = $this->OrderReponsitory->findByCondition([
            ['code', '=', $orderCode]
        ], FALSE, ['products']);
        $config = $this->config();
        $template = 'frontend.cart.success';

        $configVnpay = vnpayConfig();

        $vnp_TmnCode = $configVnpay['vnp_TmnCode']; //Mã website tại VNPAY 
        $vnp_HashSecret = $configVnpay['vnp_HashSecret']; //Chuỗi bí mật
        $vnp_Url = $configVnpay['vnp_Url'];
        $vnp_Returnurl = $configVnpay['vnp_Returnurl'];
        $vpn_apiUrl = $configVnpay['vnp_apiUrl'];
        $apiUrl = $configVnpay['apiUrl'];
        $startTime = date("YmdHis");
        $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));

        $vnp_SecureHash = $_GET['vnp_SecureHash'];
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        if ($secureHash == $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {
                
                $templatePayment = 'frontend.cart.components.vnpay';
                return view($template, compact(
                    'config',
                    'order',
                    'templatePayment',
                    'secureHash',
                    'vnp_SecureHash'
                ));
            } else {
                echo "GD Khong thanh cong";
            }
        } else {
            echo "Chu ky khong hop le";
        }
    }

    public function vnpay_ipn(){
         
    /* Payment Notify
     * IPN URL: Ghi nhận kết quả thanh toán từ VNPAY
     * Các bước thực hiện:
     * Kiểm tra checksum 
     * Tìm giao dịch trong database
     * Kiểm tra số tiền giữa hai hệ thống
     * Kiểm tra tình trạng của giao dịch trước khi cập nhật
     * Cập nhật kết quả vào Database
     * Trả kết quả ghi nhận lại cho VNPAY
     */
    
     $configVnpay = vnpayConfig();

     $vnp_TmnCode = $configVnpay['vnp_TmnCode']; //Mã website tại VNPAY 
     $vnp_HashSecret = $configVnpay['vnp_HashSecret']; //Chuỗi bí mật
     $vnp_Url = $configVnpay['vnp_Url'];
     $vnp_Returnurl = $configVnpay['vnp_Returnurl'];
     $vpn_apiUrl = $configVnpay['vnp_apiUrl'];
     $apiUrl = $configVnpay['apiUrl'];
     $startTime = date("YmdHis");
     $expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
    
    $inputData = array();
    $returnData = array();
    
    foreach ($_GET as $key => $value) {
        if (substr($key, 0, 4) == "vnp_") {
            $inputData[$key] = $value;
        }
    }
    
    $vnp_SecureHash = $inputData['vnp_SecureHash'];
    unset($inputData['vnp_SecureHash']);
    ksort($inputData);
    $i = 0;
    $hashData = "";
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
    }
    
    $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
    $vnpTranId = $inputData['vnp_TransactionNo']; //Mã giao dịch tại VNPAY
    $vnp_BankCode = $inputData['vnp_BankCode']; //Ngân hàng thanh toán
    $vnp_Amount = $inputData['vnp_Amount']/100; // Số tiền thanh toán VNPAY phản hồi
    
    $Status = 0; // Là trạng thái thanh toán của giao dịch chưa có IPN lưu tại hệ thống của merchant chiều khởi tạo URL thanh toán.
    $orderId = $inputData['vnp_TxnRef'];
    try {
        //Check Orderid    
        //Kiểm tra checksum của dữ liệu
        if ($secureHash == $vnp_SecureHash) {
            //Lấy thông tin đơn hàng lưu trong Database và kiểm tra trạng thái của đơn hàng, mã đơn hàng là: $orderId            
            //Việc kiểm tra trạng thái của đơn hàng giúp hệ thống không xử lý trùng lặp, xử lý nhiều lần một giao dịch
            //Giả sử: $order = mysqli_fetch_assoc($result);   
            $payload = [];
            $order = $this->OrderReponsitory->findByCondition([
                ['code', '=', $orderId]
            ], FALSE, ['products']);
            if ($order != NULL) {

                $orderAmount = $order->cart['cartTotal'] - $order->promotion['discount'];

                if($orderAmount == $vnp_Amount) //Kiểm tra số tiền thanh toán của giao dịch: giả sử số tiền kiểm tra là đúng. //$order["Amount"] == $vnp_Amount
                {
                    if ($order->payment != NULL && $order->payment == 'unpaid' ) {
                        if ($inputData['vnp_ResponseCode'] == '00' || $inputData['vnp_TransactionStatus'] == '00') {
                          $payload['payment'] = 'paid';
                          $payload['confirm'] = 'confirm';
                        }else {
                            $payload['payment'] = 'failed';
                            $payload['confirm'] = 'confirm';
                        }

                        $flag = $this->OrderService->updateVnpay($payload, $order);
                                    
                        $returnData['RspCode'] = '00';
                        $returnData['Message'] = 'Confirm Success';
                    } else {
                        $returnData['RspCode'] = '02';
                        $returnData['Message'] = 'Order already confirmed';
                    }
                }
                else {
                    $returnData['RspCode'] = '04';
                    $returnData['Message'] = 'invalid amount';
                }
            } else {
                $returnData['RspCode'] = '01';
                $returnData['Message'] = 'Order not found';
            }
        } else {
            $returnData['RspCode'] = '97';
            $returnData['Message'] = 'Invalid signature';
        }
    } catch (Exception $e) {
        $returnData['RspCode'] = '99';
        $returnData['Message'] = 'Unknow error';
    }
    //Trả lại VNPAY theo định dạng JSON
    echo json_encode($returnData);
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
