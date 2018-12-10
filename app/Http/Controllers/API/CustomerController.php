<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
      $customers = Customer::all();
      return response()->json(['data' => $customers],200);
      // return $this->sendResponse($customer->toArray(), 'Customer retrieved successfully');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input,[
            'name' => 'required|string|unique:customers',
            'address' => 'required',
            'phone_number' => 'required|max:13',
            'city'=> 'required'
        ]);
        if ($validator->fails()) {
          return response()->json([
            'message' => 'Could not create new Customer',
            'errors' => $validator->errors(),
            'status_code' => 400
          ], 400);
        }
        $customer = Customer::create($input);
        if($customer){
          return $this->sendResponse($customer->toArray(), 'The resource is created successfully');
        }else{
          return $this->sendCustomResponse(500, 'Internal Error');
          // return response()->json(['message' => 'Internal Error','code' => 500],500);
        }
    }

    public function update(Request $request, $id)
    {
      $input = $request->all();
      $customer = Customer::find($id);

      if (is_null($customer)) {
        return $this->sendError('Customer not found');
      }

      $customer->name = $input['name'];
      $customer->address = $input['address'];
      $customer->phone_number = $input['phone_number'];
      $customer->city = $input['city'];

      if ($customer->save()) {
        return $this->sendResponse($customer->toArray(), 'Customer updated successfully.');
      }
    }

    public function destroy(Request $request, $id)
    {
      $customer = Customer::find($id);

      if (!$customer) {
        return $this->sendError('Customer not found');
      }
      if ($customer->delete()) {
        return $this->sendCustomResponse(200, 'Successfully deleted');
      }
    }
}
