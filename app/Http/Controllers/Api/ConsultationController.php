<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ConsultationController extends Controller
{
    // Khách hàng tạo câu hỏi tư vấn
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'question' => 'required|string',
            'category' => 'required|in:product,order,return,general'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $consultation = Consultation::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'question' => $request->question,
            'category' => $request->category,
            'status' => 'pending'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã gửi câu hỏi tư vấn thành công',
            'data' => $consultation
        ], 201);
    }

    // Khách hàng xem danh sách tư vấn của mình
    public function index()
    {
        $consultations = Consultation::where('user_id', Auth::id())
            // ->with('staff:id,name,email')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $consultations
        ]);
    }

    // Xem chi tiết một tư vấn
    public function show($id)
    {
        $consultation = Consultation::with(['user:id,name,email', 'staff:id,name,email'])
            ->findOrFail($id);

        // Check permission
        if (Auth::id() !== $consultation->user_id && Auth::user()->role_id !== 1) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'data' => $consultation
        ]);
    }

    // Admin/Staff lấy tất cả câu hỏi
    public function adminIndex(Request $request)
    {
        $query = Consultation::with(['user:id,fullname,email']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->category) {
            $query->where('category', $request->category);
        }

        $consultations = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $consultations
        ]);
    }

    // Staff trả lời câu hỏi (sử dụng CKEditor)
    public function answer(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'answer' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 422);
        }

        $consultation = Consultation::findOrFail($id);
        $consultation->update([
            'answer' => $request->answer,
            'staff_id' => Auth::id(),
            'status' => 'answered'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Đã trả lời câu hỏi thành công',
            'data' => $consultation
        ]);
    }

    // Đóng câu hỏi
    public function close($id)
    {
        $consultation = Consultation::findOrFail($id);

        if (Auth::id() !== $consultation->user_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $consultation->update(['status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'Đã đóng câu hỏi'
        ]);
    }
}
