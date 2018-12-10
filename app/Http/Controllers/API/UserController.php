<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use JWTAuth;
use JWTAuthException;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

use App\Transformers\UserTransformer;

class UserController extends Controller
{
    /**
     * @var Manager
     */
    protected $fractal;

    /**
     * @var UserTransformer
     */
    private $userTransformer;

    function __construct(Manager $fractal, UserTransformer $userTransformer)
    {
        $this->fractal = $fractal;
        $this->userTransformer = $userTransformer;
    }

    public function profile(Request $request)
    {
        $users = auth()->user(); // Get users from DB
        // $users = new Item($users, $this->userTransformer); // Create a resource collection transformer
        // $users = $this->fractal->createData($users); // Transform data
        // return $users->toArray(); // Get transformed array of data
        return $this->respondWithItem($users, $this->userTransformer);

    }

  // public function profile()
  // {
  //   $user = auth()->user();
  //   return response()->json(['data' => $user])->setStatusCode(200);
  // }

  /**
   * Update the specified resource
   * @param  Request $request [description]
   * @return [type]           [description]
   */
  public function update(Request $request)
  {
    $request->validate([
      'username' => '',
      'address' => '',
      'phone_number' => 'max:13'
    ]);
    $input = $request->all();
    $account = auth()->user();

    $input['updated_at'] = \Carbon\Carbon::now('Asia/Jakarta');
    
    if ($account->update($input)) {
        return $this->sendResponse($account->toArray(), 'Profile updated successfully.');
    }
  }

  public function uploadPhoto(Request $request)
  {
    $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

    $file = $request->file('photo');
    $filename = Auth::user()->username . '.' . $file->getClientOriginalExtension();

    $file->move(public_path('images'),$filename);
    DB::table('users')
        ->where('id', Auth::user()->id)
        ->update(['photo' => url('images/'.$filename)]);

    return response()->json(['message' => 'photo uploaded', 'status_code' => 200],200);
  }

}
