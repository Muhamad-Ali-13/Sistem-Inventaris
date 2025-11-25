<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $query = Transaction::with(['item', 'user']);

        if (!$this->userHasRole(['super admin', 'admin'])) {
            $query->where('user_id', Auth::id());
        }

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%$search%")
                    ->orWhere('notes', 'like', "%$search%")
                    ->orWhereHas('item', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    })
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        if ($request->has('date') && $request->date != '') {
            $query->whereDate('transaction_date', $request->date);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('transactions.index', compact('transactions'));
    }

    public function create()
    {
        $items = Item::all();
        return view('transactions.create', compact('items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|integer|min:1',
            'type' => 'required|in:in,out',
            'notes' => 'required|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        if ($request->type == 'out') {
            $item = Item::find($request->item_id);
            if ($item->stock < $request->quantity) {
                return redirect()->back()
                    ->with('error', 'Insufficient stock. Available stock: ' . $item->stock)
                    ->withInput();
            }
        }

        $code = 'TRX-' . date('Ymd') . '-' . strtoupper(Str::random(5));

        Transaction::create([
            'code' => $code,
            'item_id' => $request->item_id,
            'user_id' => Auth::id(),
            'quantity' => $request->quantity,
            'type' => $request->type,
            'notes' => $request->notes,
            'transaction_date' => $request->transaction_date,
        ]);

        $item = Item::find($request->item_id);
        if ($request->type == 'in') {
            $item->stock += $request->quantity;
        } else {
            $item->stock -= $request->quantity;
        }
        $item->save();

        return redirect()->route('transactions.index')->with('success', 'Transaction created successfully.');
    }

    public function show(Transaction $transaction)
    {
        if (!$this->userHasRole(['super admin', 'admin']) && $transaction->user_id != Auth::id()) {
            abort(403);
        }

        return view('transactions.show', compact('transaction'));
    }
}
