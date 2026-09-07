<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Models\AstroLeadSuggestion;
use App\Models\User;
use App\Services\UserService;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use ResponseTrait;
    public $userService;
    public function __construct()
    {
        $this->userService = new UserService();
    }
    public function updateProfile(Request $request)
    {
        $request->validate([
            'phone'   => 'nullable|unique:users,phone,' . auth('sanctum')->id(),
            'name'    => 'required|string|max:255',
            'email'   => ['nullable', 'email', 'unique:users,email,' . auth('sanctum')->id()],
            'gender'  => 'nullable|in:male,female,other',
            'image'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:1024',
            'dob'     => 'nullable|date',
            'address' => 'nullable|string|max:500',
        ]);
        $user = $this->userService->updateProfile(auth('sanctum')->id(), $request);
        return $this->success(data: $user, message: 'Profile updated successfully');
    }

    public function getProfile()
    {
        $user = User::with(['country'])->find(auth('sanctum')->id());
        return $this->success(data: $user, message: 'Profile fetched successfully');
    }

    public function getAddress()
    {
        $user    = auth('sanctum')->user();
        $address = Address::where('user_id', $user->id)->latest()->get();
        return $this->success(data: $address, message: 'Address fetched successfully');
    }
    public function storeAddress(Request $request)
    {
        $request->validate([
            'address_one' => 'required|string',
            'address_two' => 'nullable|string',
            'state'       => 'required',
            'city'        => 'required',
            'pincode'     => 'required|digits:6',
            'is_default'  => 'nullable',
        ]);
        // return $request->all();
        $user            = auth('sanctum')->user();
        $default_address = Address::where(['user_id' => $user->id, 'is_default' => 'yes'])->first();
        if ($request->is_default === 'yes' && $default_address) {
            $default_address->update([
                'is_default' => 'no',
            ]);
        }
        $address = Address::create([
            'user_id'     => $user->id,
            'address_one' => $request->address_one,
            'address_two' => $request->address_two,
            'state'       => $request->state,
            'city'        => $request->city,
            'pincode'     => $request->pincode,
            'is_default'  => $request->is_default,
        ]);
        $response = [
            'success' => true,
            'message' => 'Address saved successfully!',
        ];
        return response($response, 200);
    }
    public function updateAddress(Request $request)
    {
        $request->validate([
            'id'          => 'required|numeric',
            'address_one' => 'required|string',
            'address_two' => 'nullable|string',
            'state'       => 'required|string',
            'city'        => 'required|string',
            'pincode'     => 'required|digits:6',
            'is_default'  => 'nullable',
        ]);
        $user = auth('sanctum')->user();

        $default_address = Address::where(['user_id' => $user->id, 'is_default' => 'yes'])->first();
        if ($request->is_default === 'yes' && $default_address) {
            $default_address->update([
                'is_default' => 'no',
            ]);
        }
        $address = Address::where(['id' => $request->id, 'user_id' => $user->id])->update([
            'address_one' => $request->address_one,
            'address_two' => $request->address_two,
            'state'       => $request->state,
            'city'        => $request->city,
            'pincode'     => $request->pincode,
            'is_default'  => $request->is_default,
        ]);
        $response = [
            'success' => true,
            'message' => 'Address updated successfully!',
        ];
        return response($response, 200);
    }
    public function deleteAddress(Request $request)
    {
        $request->validate([
            'id' => 'required|numeric|exists:addresses,id',
        ]);
        $user     = auth('sanctum')->user();
        $address  = Address::where('user_id', $user->id)->findOrFail($request->id)->delete();
        $response = [
            'success' => true,
            'message' => 'Address deleted successfully!',
        ];
        return response($response, 200);
    }

    public function getSuggestedAstro()
    {
        $astro  = User::findOrFail(auth('sanctum')->id());
        $astros = $astro->astroSuggestion()
            ->select('astros.id', 'astros.first_name', 'astros.middle_name', 'astros.last_name', 'astros.profile')
            ->with('services')
            ->get();
        return $this->success($astros, 'Astros retrieved successfully');
    }

    public function deleteSuggestedAstro(Request $request)
    {
        $request->validate([
            'id' => 'required',
        ]);
        AstroLeadSuggestion::where(['user_id' => auth('sanctum')->id(), 'astro_id' => $request->id])->delete();
        return $this->success(message: 'Suggestion deleted successfully!');
    }

}
