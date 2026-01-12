<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * GET semua activity log (ADMIN)
     */
    public function index()
    {
        $logs = ActivityLog::with('user')
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $logs
        ]);
    }

    /**
     * GET activity log user yang sedang login
     */
    public function myLogs(Request $request)
    {
        $logs = ActivityLog::with('user')
            ->where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $logs
        ]);
    }

    /**
     * GET detail activity log berdasarkan ID
     */
    public function show($id)
    {
        $log = ActivityLog::with('user')->find($id);

        if (!$log) {
            return response()->json([
                'status' => false,
                'message' => 'Activity log tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => true,
            'data' => $log
        ]);
    }

    /**
     * DELETE activity log (ADMIN)
     */
    public function destroy($id)
    {
        $log = ActivityLog::find($id);

        if (!$log) {
            return response()->json([
                'status' => false,
                'message' => 'Activity log tidak ditemukan'
            ], 404);
        }

        $log->delete();

        return response()->json([
            'status' => true,
            'message' => 'Activity log berhasil dihapus'
        ]);
    }
}