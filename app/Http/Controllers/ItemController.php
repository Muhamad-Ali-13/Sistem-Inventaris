<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');

        // Manual permission check
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
        $query = Item::with('category');

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('code', 'like', "%$search%")
                    ->orWhere('description', 'like', "%$search%")
                    ->orWhere('location', 'like', "%$search%")
                    ->orWhereHas('category', function ($q) use ($search) {
                        $q->where('name', 'like', "%$search%");
                    });
            });
        }

        $items = $query->paginate(10);
        $categories = Category::all(); // Tambahkan ini
        
        return view('items.index', compact('items', 'categories')); // Update compact
    }

    public function create()
    {
        $categories = Category::all();
        return view('items.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
        ]);

        try {
            // Generate kode item otomatis
            $code = 'ITM-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            // Cek jika kode sudah ada, generate ulang
            while (Item::where('code', $code)->exists()) {
                $code = 'ITM-' . date('Ymd') . '-' . strtoupper(Str::random(5));
            }

            // Method 1: Gunakan create dengan array
            $item = Item::create([
                'name' => $request->name,
                'code' => $code,
                'description' => $request->description,
                'category_id' => $request->category_id,
                'stock' => $request->stock,
                'min_stock' => $request->min_stock,
                'location' => $request->location,
            ]);

            return redirect()->route('items.index')->with('success', 'Item created successfully.');

        } catch (\Exception $e) {
            // Log error detail
            logger('Error creating item: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create item: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Item $item)
    {
        $categories = Category::all(); // Tambahkan ini
        return view('items.show', compact('item', 'categories')); // Update compact
    }

    public function edit(Item $item)
    {
        $categories = Category::all();
        return view('items.edit', compact('item', 'categories'));
    }

    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'location' => 'nullable|string|max:255',
        ]);

        $item->update($request->all());

        return redirect()->route('items.index')->with('success', 'Item updated successfully.');
    }

    public function destroy(Item $item)
    {
        if ($item->transactions()->count() > 0 || $item->itemRequests()->count() > 0) {
            return redirect()->route('items.index')
                ->with('error', 'Cannot delete item. There are transactions or requests associated with this item.');
        }

        $item->delete();
        return redirect()->route('items.index')->with('success', 'Item deleted successfully.');
    }
}