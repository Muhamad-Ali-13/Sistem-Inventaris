<?php

namespace App\Http\Controllers;

use App\Models\ItemRequest;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class ItemRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Manual permission check - alternatif tanpa middleware
        $this->middleware(function ($request, $next) {
            if (!Auth::check()) {
                return redirect()->route('login');
            }

            $user = Auth::user();

            // Safe role check
            if ($this->userHasRole($user, 'super admin')) {
                return $next($request);
            }

            if ($this->userHasRole($user, 'admin')) {
                return $next($request);
            }

            if ($this->userHasRole($user, 'karyawan')) {
                return $next($request);
            }

            // Karyawan tidak bisa akses
            abort(403, 'Unauthorized action.');
        });
    }

    /**
     * Safe method to check user role
     */
    private function userHasRole($user, $role)
    {
        // Method 1: Using hasRole if method exists
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole($role);
        }

        // Method 2: Manual check through roles
        foreach ($user->roles as $userRole) {
            if ($userRole->name === $role) {
                return true;
            }
        }

        return false;
    }

    public function index(Request $request)
    {
        $query = ItemRequest::with(['item', 'user', 'approver']);

        // Filter by user role
        if (!$this->userHasRole(Auth::user(), ['super admin', 'admin'])) {
            $query->where('user_id', Auth::id());
        }

        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('purpose', 'like', "%$search%")
                    ->orWhereHas('item', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        // Filter by status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $requests = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('item-requests.index', compact('requests'));
    }

    public function create()
    {
        $items = Item::where('stock', '>', 0)->get();
        return view('item-requests.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'purpose' => 'required|string|max:500',
        ]);

        // Check stock availability
        $item = Item::find($request->item_id);
        if ($item->stock < $request->quantity) {
            return redirect()->back()
                ->with('error', 'Insufficient stock. Available stock: ' . $item->stock)
                ->withInput();
        }

        ItemRequest::create([
            'user_id' => Auth::id(),
            'item_id' => $request->item_id,
            'quantity' => $request->quantity,
            'purpose' => $request->purpose,
        ]);

        return redirect()->route('item-requests.index')
            ->with('success', 'Item request submitted successfully.');
    }


    public function show(ItemRequest $itemRequest)
    {
        // Check permission
        if (!$this->userHasRole(Auth::user(), ['super admin', 'admin']) && $itemRequest->user_id != Auth::id()) {
            abort(403);
        }

        return view('item-requests.show', compact('itemRequest'));
    }

    public function approve(ItemRequest $itemRequest)
    {
        if (!$this->userHasRole(Auth::user(), ['super admin', 'admin'])) {
            abort(403);
        }

        // Check if already processed
        if ($itemRequest->status != 'pending') {
            return redirect()->route('item-requests.index')
                ->with('error', 'Request has already been processed.');
        }

        // Check stock availability
        if ($itemRequest->item->stock < $itemRequest->quantity) {
            return redirect()->route('item-requests.index')
                ->with('error', 'Insufficient stock to approve this request.');
        }

        $itemRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        // Update item stock
        $item = $itemRequest->item;
        $item->stock -= $itemRequest->quantity;
        $item->save();

        // Create transaction record
        \App\Models\Transaction::create([
            'code' => 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
            'item_id' => $itemRequest->item_id,
            'user_id' => $itemRequest->user_id,
            'quantity' => $itemRequest->quantity,
            'type' => 'out',
            'notes' => $itemRequest->purpose . ' (Approved Request #' . $itemRequest->id . ')',
            'transaction_date' => now(),
        ]);

        return redirect()->route('item-requests.index')->with('success', 'Item request approved successfully.');
    }

    public function reject(Request $request, ItemRequest $itemRequest)
    {
        if (!$this->userHasRole(Auth::user(), ['super admin', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        // Check if already processed
        if ($itemRequest->status != 'pending') {
            return redirect()->route('item-requests.index')
                ->with('error', 'Request has already been processed.');
        }

        $itemRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('item-requests.index')->with('success', 'Item request rejected successfully.');
    }

    public function destroy(ItemRequest $itemRequest)
    {
        // Check permission
        if (!$this->userHasRole(Auth::user(), ['super admin', 'admin']) && $itemRequest->user_id != Auth::id()) {
            abort(403);
        }

        // Only allow deletion for pending requests
        if ($itemRequest->status != 'pending') {
            return redirect()->route('item-requests.index')
                ->with('error', 'Cannot delete a processed request.');
        }

        $itemRequest->delete();

        return redirect()->route('item-requests.index')->with('success', 'Item request deleted successfully.');
    }
}
