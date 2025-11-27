<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function vnpay_payment(Request $request)
    {
        $orderId = $request->input('order_id');

        if (!$orderId) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin đơn hàng không hợp lệ'
            ], 400);
        }

        // ✅ Lấy thông tin đơn hàng từ database
        $order = Order::find($orderId);

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy đơn hàng'
            ], 404);
        }

        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = url('/api/vnpay/callback');
        $vnp_TmnCode = "XYGD1P32";
        $vnp_HashSecret = "JGB6R1WTUNI4B5NO7ZST6BMPDUEQ1L9F";

        $vnp_TxnRef = $order->id . '_' . time();
        $vnp_OrderInfo = "Thanh toan don hang #" . $order->id;
        $vnp_OrderType = "billpayment";

        $totalMoney = floatval($order->total_money);
        if ($totalMoney <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Số tiền không hợp lệ'
            ], 400);
        }

        $vnp_Amount = intval(round($totalMoney * 100));
        $vnp_Locale = 'vn';
        $vnp_BankCode = $request->input('bankCode', '');

        $vnp_IpAddr = $request->ip();
        if (!$vnp_IpAddr || $vnp_IpAddr == '' || $vnp_IpAddr == '::1' || $vnp_IpAddr == 'unknown') {
            $vnp_IpAddr = '127.0.0.1';
        }
        if (filter_var($vnp_IpAddr, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            $vnp_IpAddr = '127.0.0.1';
        }

        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        Log::info('VNPay Request Data:', $inputData);

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        Log::info('VNPay Payment URL:', ['url' => $vnp_Url]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo link thanh toán thành công',
            'data' => [
                'payment_url' => $vnp_Url
            ]
        ]);
    }

    public function vnpay_callback(Request $request)
    {
        $vnp_HashSecret = "JGB6R1WTUNI4B5NO7ZST6BMPDUEQ1L9F";
        $inputData = $request->all();

        Log::info('VNPay Callback Data:', $inputData);

        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $hashData = "";
        $i = 0;
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');

        if ($secureHash == $vnp_SecureHash) {
            if ($request->vnp_ResponseCode == '00') {
                try {
                    DB::beginTransaction();

                    $txnRef = $request->vnp_TxnRef;
                    $orderId = explode('_', $txnRef)[0];

                    $order = Order::find($orderId);

                    if ($order) {
                        // ✅ Cập nhật trạng thái thanh toán
                        $order->update([
                            'payment_status' => 'paid',
                            'paid_at' => now(),
                            'vnpay_transaction_id' => $request->vnp_TransactionNo,
                            'order_status' => 'confirmed'
                        ]);
                    }

                    DB::commit();

                    return redirect($frontendUrl . '/payment-success?order_id=' . $orderId);
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('VNPay Callback Error:', ['error' => $e->getMessage()]);
                    return redirect($frontendUrl . '/payment-failed?message=' . urlencode('Có lỗi xảy ra'));
                }
            } else {
                // ❌ Thanh toán thất bại - Đơn hàng vẫn giữ nguyên
                $txnRef = $request->vnp_TxnRef;
                $orderId = explode('_', $txnRef)[0];

                return redirect($frontendUrl . '/payment-failed?order_id=' . $orderId . '&message=' . urlencode('Thanh toán thất bại'));
            }
        } else {
            return redirect($frontendUrl . '/payment-failed?message=' . urlencode('Chữ ký không hợp lệ'));
        }
    }
}
