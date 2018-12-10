<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use App\Models\Product;

use App\Transformers\ProductTransformer;

class ProductController extends Controller
{
    /**
     * @var Manager
     */
    protected $fractal;
    /**
     * @var UserTransformer
     */
    private $userTransformer;

    function __construct(Manager $fractal, ProductTransformer $productTransformer)
    {
        $this->fractal = $fractal;
        $this->productTransformer = $productTransformer;
    }

    public function index()
    {
      $product = auth()->user()->product->sortBy('DESC');
      return $this->respondWithCollection($product, $this->productTransformer);
      // return response()->json(['data' => $product],200);
    }

    public function show($id)
    {
      $product = Product::find($id);

      if (!$product) {
        return $this->sendError('Could not found the product');
      }
      return $this->respondWithItem($product, $this->productTransformer);
      // return response()->json(['data' => $product]);
    }

    public function store(Request $request)
    {
      $input = $request->all();
      $user = auth()->user();
      $validator = Validator::make($input,[
        'product_code' => 'required|integer|unique:products|digits_between:1,20',
        'name' => 'required|unique:products',
        'category_id' => 'required|exists:categories,category_id',
        'buy_price' => 'required',
        'sell_price' => 'required',
        'unit' => 'required|string',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'message' => 'Could not create new product',
          'errors' => $validator->errors(),
          'status_code' => 400
        ], 400);
      }
      $input['stock'] = 0;
      $product = $user->product()->create($input);
      if($product){
        return $this->setStatusCode(201)->respondWithItem($product, $this->productTransformer);
        // return $this->sendResponse($product->toArray(), 'The resource is created successfully');
        // return response()->json(['data' => $product,'message' => 'The resource is created successfully'],201);
      }
    }

    public function update(Request $request, $id)
    {
      $input = $request->all();
      $product = Product::find($id);

      if (is_null($product)) {
        return $this->sendError("Product with id {$id} doesn't exist");
      } 

      $request->validate([
        'name' => '',
        'stock' => 'integer',
        'category_id' => 'integer',
        'buy_price' => 'integer',
        'sell_price' => 'integer',
        'unit' => 'string',
      ]);

      $product->name = $input['name'];
      $product->stock = $input['stock'];
      $product->category_id = $input['category_id'];
      $product->buy_price = $input['buy_price'];
      $product->sell_price = $input['sell_price'];
      $product->unit = $input['unit'];

      if ($product->save()) {
        return $this->sendResponse($product->toArray(), 'Product updated successfully.');
      }
    }

    public function destroy($id)
    {
      $product = Product::find($id);
      if (!$product) {
        return $this->sendError('Product not found');
      }

      if ($product->delete()) {
        return $this->sendCustomResponse(200, 'successfully deleted');
      }
    }
}
