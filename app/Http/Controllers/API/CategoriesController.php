<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Categories;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

use App\Transformers\CategoriesTransformer;

class CategoriesController extends Controller
{
    /**
     * @var Manager
     */
    protected $fractal;
    /**
     * @var UserTransformer
     */
    private $userTransformer;

    function __construct(Manager $fractal, CategoriesTransformer $categoriesTransformer)
    {
        $this->fractal = $fractal;
        $this->categoriesTransformer = $categoriesTransformer;
    }

    public function index()
    {
      $category = Categories::all();
      // return $this->sendResponse($category->toArray(), 200);
      // return response()->json(['data' => $category],200);
      return $this->respondWithCollection($category, $this->categoriesTransformer);

    }

    public function store(Request $request)
    {
      $input = $request->all();
      $validator = Validator::make($input,[
        'name' => 'required|max:50|unique:categories'
      ]);

      if ($validator->fails()) {
        return response()->json([
          'message' => 'Could not create new category',
          'errors' => $validator->errors(),
          'status_code' => 400
        ], 400);
      }
      $category = Categories::create($input);
      if($category){
          // return response()->json(['status' => 'The resource is created successfully'], 201);
          return $this->sendResponse($category->toArray(), 'The resource is created successfully');
      }else{
        // return response()->json(['message' => 'Internal Error','code' => 500],500);
        return $this->sendCustomResponse(500, 'Internal Error');
      }
    }

    public function destroy($id)
    {
        $categories = Categories::findOrFail($id);
        $categories->delete();
        return response()->json(['success' => 'Kategori: ' . $categories->name . ' Telah Dihapus'],200);
    }
}
