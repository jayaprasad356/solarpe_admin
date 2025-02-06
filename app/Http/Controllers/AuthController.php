<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Users;
use App\Models\Bankdetails;
use App\Models\Upis;
use App\Models\Avatars;
use App\Models\Coins;
use App\Models\SpeechText;  
use App\Models\Appsettings; 
use App\Models\Ratings; 
use App\Models\Gifts;
use App\Models\Transactions;
use App\Models\DeletedUsers; 
use App\Models\Withdrawals;  
use App\Models\UserCalls;
use App\Models\explaination_video;
use App\Models\explaination_video_links;
use Carbon\Carbon;
use App\Models\News; 
use Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;


class AuthController extends Controller
{

//     public function withdrawals(Request $request)
// {
//     // Validate input
//    // Validate input with custom error messages
//     $messages = [
//         'mobile.required' => 'The mobile number is required.',
//         'mobile.digits' => 'The mobile number must be exactly 10 digits.',
//         'account_num.required' => 'The account number is required.',
//         'ifsc_code.required' => 'The IFSC code is required.',
//         'ifsc_code.max' => 'The IFSC code must not exceed 11 characters.',
//         'branch.required' => 'The branch name is required.',
//         'bank.required' => 'The bank name is required.',
//         'amount.required' => 'The amount is required.',
//         'amount.numeric' => 'The amount must be a valid number.',
//         'amount.min' => 'The amount must be at least 1.',
//         'datetime.required' => 'The date and time are required.',
//         'datetime.date' => 'The date and time must be a valid date.',
//     ];

//     $request->validate([
//         'mobile' => 'required|digits:10',
//         'account_num' => 'required|string',
//         'ifsc_code' => 'required|string|max:11',
//         'branch' => 'required|string',
//         'bank' => 'required|string',
//         'amount' => 'required|numeric|min:1',
//         'datetime' => 'required|date',
//     ], $messages);

//     // Insert into withdrawals table
//     $withdrawal = new Withdrawals();
//     $withdrawal->mobile = $request->mobile;
//     $withdrawal->account_num = $request->account_num;
//     $withdrawal->ifsc_code = $request->ifsc_code;
//     $withdrawal->branch = $request->branch;
//     $withdrawal->bank = $request->bank;
//     $withdrawal->amount = $request->amount;
//     $withdrawal->datetime = $request->datetime;
    
//     if ($withdrawal->save()) {
//         return response()->json([
//             'success' => true,
//             'message' => 'Withdrawal request submitted successfully.',
//             'data' => [
//                 'id' => $withdrawal->id,
//                 'user_id' => $withdrawal->user_id,
//                 'mobile' => $withdrawal->mobile,
//                 'account_number' => $withdrawal->account_number,
//                 'ifsc_code' => $withdrawal->ifsc_code,
//                 'branch' => $withdrawal->branch,
//                 'bank' => $withdrawal->bank,
//                 'amount' => number_format($withdrawal->amount, 2),
//                 'datetime' => $withdrawal->datetime->format('Y-m-d H:i:s'),
//             ],
//         ], 200);
//     }

//     return response()->json([
//         'success' => false,
//         'message' => 'Failed to submit withdrawal request.'
//     ], 500);
// }
public function withdrawals(Request $request)
{
    // Get input values
    $mobile = $request->input('mobile');
    $account_num = $request->input('account_num');
    $ifsc = $request->input('ifsc');
    $branch = $request->input('branch');
    $bank = $request->input('bank');
    $holder_name = $request->input('holder_name');
    $amount = $request->input('amount');
    $datetime = $request->input('datetime'); 

    // Validate input with individual error messages
    if (empty($mobile)) {
        return response()->json([
          'success' => false, 
          'message' => 'Mobile number is required.'], 
         200);
    }
    if (empty($account_num)) {
        return response()->json([
            'success' => false, 
            'message' => 'Account num is required.'], 
        200);
    }
    if (empty($ifsc)) {
        return response()->json([
            'success' => false, 
            'message' => 'IFSC is required.'
        ], 200);
    }

    if (empty($branch)) {
        return response()->json([
            'success' => false, 
            'message' => 'Branch is required.'], 
        200);
    }
    if (empty($bank)) {
        return response()->json([
            'success' => false, 
            'message' => 'Bank is required.'], 
        200);
    }
    if (empty($holder_name)) {
        return response()->json([
            'success' => false, 
            'message' => 'Holder Name is required.'], 
        200);
    }
    if (empty($amount)) {
        return response()->json([
            'success' => false, 
            'message' => 'Amount is required.'], 
        200);
    }
    if (empty($datetime)) {
        return response()->json([
            'success' => false, 
            'message' => 'Date and time are required.'
        ], 200);
    } elseif (!strtotime($datetime)) {
        return response()->json([
            'success' => false, 
            'message' => 'Date and time must be a valid date in the format yyyy-mm-dd h-m-s.'
        ], 200);
    }



    // Insert into withdrawals table
    $withdrawal = new Withdrawals();
    $withdrawal->mobile = $mobile;
    $withdrawal->account_num = $account_num;
    $withdrawal->ifsc = $ifsc;
    $withdrawal->branch = $branch;
    $withdrawal->bank = $bank;
    $withdrawal->amount = $amount;
    $withdrawal->holder_name = $holder_name;
    $withdrawal->datetime = $datetime;

    if ($withdrawal->save()) {
        return response()->json([
            'success' => true,
            'message' => 'Withdrawal inserted successfully.',
            'data' => [
                'id' => $withdrawal->id,
                'mobile' => $withdrawal->mobile,
                'account_number' => $withdrawal->account_num,
                'ifsc' => $withdrawal->ifsc,
                'branch' => $withdrawal->branch,
                'bank' => $withdrawal->bank,
                'amount' => $withdrawal->amount,
                'holder_name' => $withdrawal->holder_name,
                'datetime' => $withdrawal->datetime,
                'updated_at' => $withdrawal->updated_at->format('Y-m-d H:i:s'),
                'created_at' => $withdrawal->created_at->format('Y-m-d H:i:s'),
            ],
        ], 200);
    }

    return response()->json([
        'success' => false,
        'message' => 'Failed to insert withdrawal.',
    ], 500);
}

public function add_bank_details(Request $request)
{
    // Get input values
    $mobile = $request->input('mobile');
    $account_num = $request->input('account_num');
    $ifsc = $request->input('ifsc');
    $branch = $request->input('branch');
    $bank = $request->input('bank');
    $holder_name = $request->input('holder_name');

    // Validate input with individual error messages
    if (empty($mobile)) {
        return response()->json([
          'success' => false, 
          'message' => 'Mobile number is required.'], 
         200);
    }
    if (empty($account_num)) {
        return response()->json([
            'success' => false, 
            'message' => 'Account num is required.'], 
        200);
    }
    if (empty($ifsc)) {
        return response()->json([
            'success' => false, 
            'message' => 'IFSC is required.'
        ], 200);
    }

    if (empty($branch)) {
        return response()->json([
            'success' => false, 
            'message' => 'Branch is required.'], 
        200);
    }
    if (empty($bank)) {
        return response()->json([
            'success' => false, 
            'message' => 'Bank is required.'], 
        200);
    }
    if (empty($holder_name)) {
        return response()->json([
            'success' => false, 
            'message' => 'Holder Name is required.'], 
        200);
    }
 


    // Insert into withdrawals table
    $bankdetails = new Bankdetails();
    $bankdetails->mobile = $mobile;
    $bankdetails->account_num = $account_num;
    $bankdetails->ifsc = $ifsc;
    $bankdetails->branch = $branch;
    $bankdetails->bank = $bank;
    $bankdetails->holder_name = $holder_name;

    if ($bankdetails->save()) {
        return response()->json([
            'success' => true,
            'message' => 'Bank Details inserted successfully.',
            'data' => [
                'id' => $bankdetails->id,
                'mobile' => $bankdetails->mobile,
                'account_number' => $bankdetails->account_num,
                'ifsc' => $bankdetails->ifsc,
                'branch' => $bankdetails->branch,
                'bank' => $bankdetails->bank,
                'holder_name' => $bankdetails->holder_name,
                'updated_at' => $bankdetails->updated_at->format('Y-m-d H:i:s'),
                'created_at' => $bankdetails->created_at->format('Y-m-d H:i:s'),
            ],
        ], 200);
    }

    return response()->json([
        'success' => false,
        'message' => 'Failed to insert bank details.',
    ], 500);
}

}