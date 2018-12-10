<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

use App\Models\OrderDetail;

class OrderDetailController extends Controller
{
    public function store(Reequest $request)
    {
    	$input = $request->all();
    	$validator = Validator::create($input, [
    		'date' => 'required|date|date_format:Y-m-d',
    		'product_amount' => 'required|numeric',
    		'supplier_id' => 'required|exists:suppliers,supplier_id'
    	]);

    	if ($validator->fails()) {
    		return response()->json($input,[
    			 'message' => 'Could not create new Customer',
	            'errors' => $validator->errors(),
	            'status_code' => 400
    		],400);
    	}

    	$orderdtl = auth()->user()->create($input);
    	if ($orderdtl) {
    		return response()->json(['data' => $orderdtl, 'message' => 'successfully created resource'],201);
    	} else {
    		return response()->json(['error' => 'internal error'],500);
    	}
    }
}
