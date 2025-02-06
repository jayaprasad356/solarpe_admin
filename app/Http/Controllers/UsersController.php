<?php

namespace App\Http\Controllers;

use App\Models\Users;
use App\Models\Avatars;
use App\Models\Transactions;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    // List all users with optional search functionality
    public function index(Request $request)
    {
        $search = $request->get('search');
    
        $users = Users::query()
            ->when($search, function ($query, $search) {
                return $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                          ->orWhere('mobile', 'like', '%' . $search . '%')
                          ->orWhere('referred_by', 'like', '%' . $search . '%')
                          ->orWhere('refer_code', 'like', '%' . $search . '%')
                          ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->orderBy('created_at', 'desc') // Use `created_at` instead of `datetime` if applicable
            ->get();
    
        return view('users.index', compact('users'));
    }
    
    // Show the form to edit an existing user
    public function edit($id)
    {
        $user = Users::findOrFail($id);

        return view('users.edit', compact('user'));
    }

    // Update an existing user
    public function update(Request $request, $id)
    {
        $user = Users::findOrFail($id);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->mobile = $request->mobile;
        $user->state = $request->state; 
        $user->age = $request->age;
        $user->city = $request->city;
        $user->referred_by = $request->referred_by;
        $user->refer_code = $request->refer_code;
        $user->password = $request->password; 
        $user->account_num = $request->account_num;
        $user->holder_name = $request->holder_name; 
        $user->ifsc = $request->ifsc; 
        $user->bank = $request->bank;
        $user->branch = $request->branch;
        $user->withdrawal_status = $request->withdrawal_status; 
        $user->recharge = $request->recharge; 
        $user->total_recharge = $request->total_recharge; 
        $user->total_income = $request->total_income;
        $user->today_income = $request->today_income; 
        $user->device_id = $request->device_id; 
        $user->total_withdrawal = $request->total_withdrawal; 
        $user->team_income = $request->team_income;
        $user->earning_wallet = $request->earning_wallet; 
        $user->bonus_wallet = $request->bonus_wallet; 
        $user->balance = $request->balance; 
        $user->registered_datetime = $request->registered_datetime; 
        $user->blocked = $request->blocked; 
        $user->save();

        return redirect()->route('users.index')->with('success', 'user successfully updated.');
    }

    // Delete a user
    public function destroy($id)
    {
        $user = Users::findOrFail($id);
        $user->delete();

        return redirect()->route('users.index')->with('success', 'user successfully deleted.');
    }

    // Handle Add Coins form submission
    public function addRecharge(Request $request, $id)
    {
        // Validate the input
        $request->validate([
            'recharge' => 'required|numeric|min:1',
        ]);

        $user = Users::findOrFail($id); // Retrieve the user by ID

        // Update the user's coins
        $user->recharge += $request->input('recharge');
        $user->total_recharge += $request->input('recharge');
        $user->save();

        // Create a new transaction record
        Transactions::create([
            'user_id' => $user->id,
            'type' => 'recharge',
            'amount' => $request->input('recharge'),
            'datetime' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'Recharge Added Successfully.');
    }
     // Handle Add Coins form submission
     public function addBonus(Request $request, $id)
     {
         // Validate the input
         $request->validate([
             'bonus' => 'required|numeric|min:1',
         ]);
 
         $user = Users::findOrFail($id); // Retrieve the user by ID
 
         // Update the user's coins
         $user->balance += $request->input('bonus');
         $user->today_income += $request->input('balance');
         $user->total_income += $request->input('balance');
         $user->save();
 
         // Create a new transaction record
         Transactions::create([
            'user_id' => $user->id,
            'type' => 'bonus',
            'amount' => $request->input('bonus'),
            'datetime' => now(),
         ]);
 
         return redirect()->route('users.index')->with('success', 'Bonus Added Successfully.');
     }

}
