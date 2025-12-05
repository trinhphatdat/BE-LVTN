<?php

namespace App\Http\Controllers;

use App\Models\ReturnRequest;
use App\Models\ReturnRequestImage;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReturnRequestController extends Controller
{
    // Client: Tạo yêu cầu trả hàng
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id' => 'required|exists:orders,id',
            'reason' => 'required|string',
            'custom_note' => 'nullable|string',
            'bank_name' => 'required|string',
            'bank_account_number' => 'required|string',
            'bank_account_name' => 'required|string',
            'images' => 'required|array|min:1',
            'images.*.image' => 'required|image|max:5120',
            'images.*.description' => 'nullable|string',
        ]);

        // Kiểm tra đơn hàng đã được giao
        $order = Order::find($validated['order_id']);
        if ($order->order_status !== 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể trả hàng cho đơn hàng đã được giao'
            ], 400);
        }

        // Kiểm tra trong vòng 7 ngày
        $deliveredDate = new \DateTime($order->delivered_at);
        $now = new \DateTime();
        $diff = $now->diff($deliveredDate);

        if ($diff->days > 7) {
            return response()->json([
                'success' => false,
                'message' => 'Chỉ có thể yêu cầu trả hàng trong vòng 7 ngày kể từ khi nhận hàng'
            ], 400);
        }

        // Kiểm tra đã có yêu cầu chưa
        $existingRequest = ReturnRequest::where('order_id', $validated['order_id'])
            ->whereIn('status', ['pending', 'approved', 'received'])
            ->exists();

        if ($existingRequest) {
            return response()->json([
                'success' => false,
                'message' => 'Đơn hàng này đã có yêu cầu trả hàng đang xử lý'
            ], 400);
        }

        DB::beginTransaction();
        try {
            // Tạo return request - trả toàn bộ đơn hàng
            $returnRequest = ReturnRequest::create([
                'order_id' => $validated['order_id'],
                'user_id' => auth()->id(),
                'return_type' => 'full',
                'reason' => $validated['reason'],
                'custom_note' => $validated['custom_note'] ?? null,
                'status' => 'pending',
                'bank_name' => $validated['bank_name'],
                'bank_account_number' => $validated['bank_account_number'],
                'bank_account_name' => $validated['bank_account_name'],
            ]);

            // Upload hình ảnh
            foreach ($validated['images'] as $imageData) {
                $image = $imageData['image'];
                $path = $image->store('return_requests', 'public');

                ReturnRequestImage::create([
                    'return_request_id' => $returnRequest->id,
                    'image_url' => $path,
                    'description' => $imageData['description'] ?? null,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Gửi yêu cầu trả hàng thành công. Chúng tôi sẽ xem xét và phản hồi trong thời gian sớm nhất.',
                'data' => $returnRequest->load('returnRequestImages')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    // Client: Lấy danh sách yêu cầu của user
    public function getUserRequests()
    {
        $requests = ReturnRequest::with(['order.orderDetails.productVariant.product', 'order.orderDetails.productVariant.size', 'order.orderDetails.productVariant.color', 'returnRequestImages'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    // Admin: Lấy tất cả yêu cầu
    public function adminGetRequests(Request $request)
    {
        $query = ReturnRequest::with([
            'user',
            'order',
            'returnRequestImages'
        ]);

        if ($request->status && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $requests
        ]);
    }

    // Admin: Chi tiết yêu cầu
    public function adminGetRequestDetail($id)
    {
        $request = ReturnRequest::with([
            'user',
            'order.orderDetails.productVariant.product',
            'order.orderDetails.productVariant.size',
            'order.orderDetails.productVariant.color',
            'returnRequestImages'
        ])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $request
        ]);
    }

    // Admin: Duyệt yêu cầu
    public function adminApproveRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_note' => 'nullable|string',
            'refund_amount' => 'required|numeric|min:0',
        ]);

        $returnRequest = ReturnRequest::findOrFail($id);

        if ($returnRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu này không thể duyệt'
            ], 400);
        }

        $returnRequest->update([
            'status' => 'approved',
            'admin_id' => auth()->id(),
            'admin_note' => $validated['admin_note'] ?? null,
            'refund_amount' => $validated['refund_amount'],
            'approved_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã duyệt yêu cầu trả hàng',
            'data' => $returnRequest
        ]);
    }

    // Admin: Từ chối yêu cầu
    public function adminRejectRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_note' => 'required|string',
        ]);

        $returnRequest = ReturnRequest::findOrFail($id);

        if ($returnRequest->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu này không thể từ chối'
            ], 400);
        }

        $returnRequest->update([
            'status' => 'rejected',
            'admin_id' => auth()->id(),
            'admin_note' => $validated['admin_note'],
            'rejected_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã từ chối yêu cầu trả hàng',
            'data' => $returnRequest
        ]);
    }

    // Admin: Xác nhận đã nhận hàng trả
    public function adminConfirmReceived(Request $request, $id)
    {
        $validated = $request->validate([
            'admin_note' => 'nullable|string',
        ]);

        $returnRequest = ReturnRequest::findOrFail($id);

        if ($returnRequest->status !== 'approved') {
            return response()->json([
                'success' => false,
                'message' => 'Yêu cầu chưa được duyệt'
            ], 400);
        }

        $returnRequest->update([
            'status' => 'received',
            'admin_note' => $validated['admin_note'] ?? $returnRequest->admin_note,
            'received_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã xác nhận nhận hàng',
            'data' => $returnRequest
        ]);
    }

    // Admin: Hoàn tiền
    public function adminRefund(Request $request, $id)
    {
        $validated = $request->validate([
            'refund_amount' => 'required|numeric|min:0',
            'admin_note' => 'nullable|string',
        ]);

        $returnRequest = ReturnRequest::findOrFail($id);

        if ($returnRequest->status !== 'received') {
            return response()->json([
                'success' => false,
                'message' => 'Chưa nhận được hàng trả'
            ], 400);
        }

        $returnRequest->update([
            'status' => 'completed',
            'refund_status' => 'completed',
            'refund_amount' => $validated['refund_amount'],
            'admin_note' => $validated['admin_note'] ?? $returnRequest->admin_note,
            'refunded_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã hoàn tiền thành công',
            'data' => $returnRequest
        ]);
    }
}
