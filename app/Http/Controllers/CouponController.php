<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    /**
     * Display a listing of the coupons.
     */
    public function index()
    {
        $coupon = Coupon::latest()->paginate(10);
        return view('admin.coupon.index', compact('coupon'));
    }

    /**
     * Show the form for creating a new coupon.
     */
    public function create()
    {
        
        return view('admin.coupon.form', ['coupon' => null]);
    }

    /**
     * Store a newly created coupon in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code|max:255',
            'discount_percentage' => 'required|integer|min:1|max:100',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'status' => 'required|string',
            'is_active' => 'boolean',
        ]);

        Coupon::create([
            'code' => strtoupper($request->code),
            'discount_percentage' => $request->discount_percentage,
            'max_uses' => $request->max_uses,
            'expires_at' => $request->expires_at,
            'status' => $request->status,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect('/dashboard/coupon')->with('success', 'Coupon created successfully!');
    }

    /**
     * Show the form for editing the specified coupon.
     */
    public function edit(Coupon $coupon)
    {
        return view('admin.coupon.form', compact('coupon'));
    }

    /**
     * Update the specified coupon in storage.
     */
    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code' => 'required|string|unique:coupons,code,' . $coupon->id . '|max:255',
            'discount_percentage' => 'required|integer|min:1|max:100',
            'max_uses' => 'nullable|integer|min:1',
            'expires_at' => 'nullable|date',
            'status' => 'required|string',
            'is_active' => 'boolean',
        ]);

        $coupon->update([
            'code' => strtoupper($request->code),
            'discount_percentage' => $request->discount_percentage,
            'max_uses' => $request->max_uses,
            'expires_at' => $request->expires_at,
            'status' => $request->status,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect('/dashboard/coupon')->with('success', 'Coupon updated successfully!');
    }

    /**
     * Remove the specified coupon from storage.
     */
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect('/dashboard/coupon')->with('success', 'Coupon deleted successfully!');
    }
}
