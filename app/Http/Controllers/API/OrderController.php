<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;

use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use App\Transformers\OrderTransformer;

class OrderController extends Controller
{
    protected $fractal;

    /**
     * @var orderTransformer
     */
    private $orderTransformer;

    public function __construct(Manager $fractal, OrderTransformer $orderTransformer)
    {
        $this->fractal = $fractal;
        $this->orderTransformer = $orderTransformer;
    }
    public function index()
    {
        $orders = Order::all()->sortBy('created_at');
        return $this->respondWithCollection($orders, $this->orderTransformer);
        // return response()->json(['data' => $orders, 'code' => 200],200);
    }

    public function show($id)
    {
        $products = Product::findOrFail($id);
        return response()->json($products, 200);
    }

    //table orders
    public function store(Request $request)
    {
    	$input = $request->all();
    	$user = auth()->user();
    	$validator = Validator::make($input, [
    		'supplier_id' => 'required|integer|exists:suppliers,supplier_id'
    	]);

    	if ($validator->fails()) {
	        return response()->json([
	          'message' => 'Could not create new Order',
	          'errors' => $validator->errors(),
	          'status_code' => 400
	        ], 400);
	    }

	    $order = $user->order()->create([
            'order_id' => $request->order_id,
            'supplier_id' => $request->supplier_id,
        ]);
    	if ($order) {
            return $this->setStatusCode(201)->respondWithItem($order, $this->orderTransformer);
    		// return response()->json(['data' => $order, 'message' => 'successfully created resource'],201);
    	} else {
    		return response()->json(['error' => 'internal error'],500);
    	}
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'product_code' => 'required|exists:products,product_code',
            'product_amount' => 'required|integer'
        ]);
        
        try {
            $order = Order::find($id);
            $orderdetail = OrderDetail::find($id);
            if (is_null($order)) {
                return $this->sendError("Product with id {$id} doesn't exist");
            } 
            //select berdasarkan table product berdasar product code
            $product = Product::find($request->product_code);
            //select dari table order_details berdasar product_code & order_id
            $order_details = $order->orderdetail()->where('product_code', $product->product_code);
            $order_detail = $order_details->first();
            if ($order_detail) {
                $order_detail->update([
                    //jika data ada diupdate data product amount
                    'product_amount' => $order_detail->product_amount + $request->product_amount,
                    // 'subtotal_price' => $order_detail->product_amount * $product->buy_price
                ]);
            } else {
                OrderDetail::create([
                    'order_id' => $order->order_id,
                    'product_code' => $request->product_code,
                    'product_amount' => $request->product_amount,
                    'buy_price' => $product->buy_price,
                    'subtotal_price' => $request->product_amount * $product->buy_price
                ]);
            }
            
            $order_id = $order_details->where('order_id', $id)->first();
            $product_code = DB::table('order_details')
                ->where('product_code', $product->product_code)
                ->get()->all();
                
            $stock = 0;
            foreach ($product_code as $key) {
                $stock+=$key->product_amount;
            }
            // $stock = json_decode(json_encode($produkcode),true);
            DB::table('order_details')
                ->where('order_id', $id)
                ->where('product_code', $request->product_code)
                ->update([
                    'subtotal_price' => $order_id->product_amount * $product->buy_price,
                ]);

            DB::table('products')->where('product_code', $request->product_code)->update([
                'stock' => $stock
            ]);
            return $this->sendResponse($order_detail->toArray(), 'Order updated successfully.');
            // return response()->json(['success' => 'Product Telah Ditambahkan', 'code' => 200],200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
