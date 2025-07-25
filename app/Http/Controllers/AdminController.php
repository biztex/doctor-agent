<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ChatSession;
use App\Models\ChatMessage;
use App\Models\Disease;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_sessions' => ChatSession::count(),
            'level1_diseases' => Disease::where('urgency_level', 'レベル1')->count(),
            'level2_diseases' => Disease::where('urgency_level', 'レベル2')->count(),
            'level3_diseases' => Disease::where('urgency_level', 'レベル3')->count(),
        ];

        return view('admin.index', compact('stats'));
    }

    public function users(Request $request)
    {
        $query = User::query();
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.users', compact('users'));
    }

    public function updateUser(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required|in:admin,user'
        ]);

        $user = User::findOrFail($id);
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return response()->json(['success' => false, 'message' => '自分自身を削除することはできません。']);
        }

        $user->delete();
        return response()->json(['success' => true]);
    }

    public function urgencyManagement()
    {
        $diseases = Disease::orderBy('urgency_level')->orderBy('name')->get();
        $level1Diseases = $diseases->where('urgency_level', 'レベル1');
        $level2Diseases = $diseases->where('urgency_level', 'レベル2');
        $level3Diseases = $diseases->where('urgency_level', 'レベル3');

        return view('admin.urgency-management', compact('level1Diseases', 'level2Diseases', 'level3Diseases'));
    }

    public function addDisease(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'urgency_level' => 'required|in:レベル1,レベル2,レベル3'
        ]);

        Disease::create([
            'name' => $request->name,
            'urgency_level' => $request->urgency_level
        ]);

        return response()->json(['success' => true]);
    }

    public function deleteDisease($id)
    {
        $disease = Disease::findOrFail($id);
        $disease->delete();
        return response()->json(['success' => true]);
    }

    public function resetDiseases(Request $request)
    {
        $request->validate([
            'urgency_level' => 'required|in:レベル1,レベル2,レベル3'
        ]);

        Disease::where('urgency_level', $request->urgency_level)->delete();
        return response()->json(['success' => true]);
    }

    public function consultationHistory(Request $request)
    {

        $query = \App\Models\ChatSession::with(['user', 'messages']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('urgency_level') && $request->urgency_level !== 'all') {
            $query->where('urgency_level', $request->urgency_level);
        }

        $sessions = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.consultation-history', compact('sessions'));
    }

    public function viewConsultation($id)
    {
        $session = \App\Models\ChatSession::with(['user', 'messages'])->findOrFail($id);
        return view('admin.view-consultation', compact('session'));
    }

    public function deleteConsultation($id)
    {
        $session = \App\Models\ChatSession::findOrFail($id);
        $session->delete();
        return response()->json(['success' => true]);
    }
} 