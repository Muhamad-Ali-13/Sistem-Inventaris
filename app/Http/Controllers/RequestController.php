<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class RequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(ItemRequest $request)
    {
        $query = ItemRequest::with(['item', 'user', 'approver']);

        if (!$this->userHasRole(['super admin', 'admin'])) {
            $query->where('user_id', Auth::id());
        }

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

    public function store(ItemRequest $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'purpose' => 'required|string|max:500',
        ]);

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
            'status' => 'pending',
        ]);

        return redirect()->route('item-requests.index')->with('success', 'Item request submitted successfully.');
    }

    public function show(ItemRequest $itemRequest)
    {
        if (!$this->userHasRole(['super admin', 'admin']) && $itemRequest->user_id != Auth::id()) {
            abort(403);
        }

        return view('item-requests.show', compact('itemRequest'));
    }

    public function approve(ItemRequest $itemRequest)
    {
        if (!$this->userHasRole(['super admin', 'admin'])) {
            abort(403);
        }

        if ($itemRequest->status != 'pending') {
            return redirect()->route('item-requests.index')
                ->with('error', 'Request has already been processed.');
        }

        if ($itemRequest->item->stock < $itemRequest->quantity) {
            return redirect()->route('item-requests.index')
                ->with('error', 'Insufficient stock to approve this request.');
        }

        $itemRequest->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        $item = $itemRequest->item;
        $item->stock -= $itemRequest->quantity;
        $item->save();

        \App\Models\Transaction::create([
            'code' => 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5)),
            'item_id' => $itemRequest->item_id,
            'user_id' => $itemRequest->user_id,
            'quantity' => $itemRequest->quantity,
            'type' => 'out',
            'notes' => $itemRequest->purpose,
            'transaction_date' => now(),
        ]);

        return redirect()->route('item-requests.index')->with('success', 'Item request approved successfully.');
    }

    public function reject(ItemRequest $request, ItemRequest $itemRequest)
    {
        if (!$this->userHasRole(['super admin', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

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
}
