<?php
namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Models\Astro;
use App\Models\AstroBusinessSetting;
use App\Models\Banner;
use App\Models\BusinessSetting;
use App\Models\City;
use App\Models\Country;
use App\Models\Language;
use App\Models\Service;
use App\Models\Skill;
use App\Models\State;
use App\Models\User;
use App\Models\WalletPackage;
use App\Traits\ResponseTrait;
use Illuminate\Http\Request;

class BasicController extends Controller
{
    use ResponseTrait;
    public function businessSettings()
    {
        $businessSettings = BusinessSetting::pluck('value', 'key')->toArray();
        return $this->success(data: $businessSettings, message: 'Business settings fetched successfully');
    }
    public function astroBusinessSettings()
    {
        $businessSettings = AstroBusinessSetting::pluck('value', 'key')->toArray();
        return $this->success(data: $businessSettings, message: 'Astro business settings fetched successfully');
    }

    public function getBanners()
    {
        $banners = Banner::active()->with(['service'])->get();
        return $this->success(data: $banners, message: 'Banners fetched successfully');
    }
    public function getServices(Request $request)
    {
        $request->validate([
            'search' => 'nullable',
        ]);
        $services = Service::active()->with(['astros' => function ($query) {
            $query->select('astros.id', 'profile')->take(5);
        }])
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%');
            })
            ->orderBy('rank', 'asc')->get();
        return $this->success(data: $services, message: 'Services fetched successfully');
    }

    public function getCountries()
    {
        $countries = Country::all();
        return $this->success(data: $countries, message: 'Countries fetched successfully');
    }
    public function getStates()
    {
        $states = State::where('country_id', auth('sanctum')->user()->country_id)->get();
        return $this->success(data: $states, message: 'States fetched successfully');
    }
    public function getCities(Request $request)
    {
        $request->validate([
            'state_id' => 'required|exists:states,id',
        ]);
        $cities = City::where('state_id', $request->state_id)->get();
        return $this->success(data: $cities, message: 'Cities fetched successfully');
    }

    // public function walletPackages()
    // {
    //     $user = User::with(['country'])->findOrFail(auth('sanctum')->id());
    //     if ($user->country->currency == 'INR') {
    //         $newUser = User::withCount('walletTransaction')->where('id', auth('sanctum')->id())->first();
    //         if ($newUser->wallet_transaction_count == 0) {
    //             $newPackage = WalletPackage::active()->where(['for_new_user' => 'yes', 'type' => 'national'])->get();
    //         }
    //         $walletPackages = WalletPackage::active()->where('amount', '>', 0)
    //             ->where(['for_new_user' => 'no', 'type' => 'national'])->get();
    //         $walletPackages = isset($newPackage) ? $newPackage->merge($walletPackages) : $walletPackages;
    //         return $this->success(data: $walletPackages, message: 'Wallet Packages fetched successfully');
    //     } else {
    //         $newUser = User::withCount('walletTransaction')->where('id', auth('sanctum')->id())->first();
    //         if ($newUser->wallet_transaction_count == 0) {
    //             $newPackage = WalletPackage::active()->where(['for_new_user' => 'yes', 'type' => 'international'])->get();
    //         }
    //         $walletPackages = WalletPackage::active()->where('amount_dollar', '>', 0)
    //             ->where(['for_new_user' => 'no', 'type' => 'international'])->get();
    //         $walletPackages = isset($newPackage) ? $newPackage->merge($walletPackages) : $walletPackages;
    //         return $this->success(data: $walletPackages, message: 'Wallet Packages fetched successfully');
    //     }
    // }

    public function walletPackages()
    {
        $user = User::withCount('walletTransaction')->where('id', auth('sanctum')->id())->first();
        if ($user->country_id == 101) {
            $amountColumn = 'amount';
        } else {
            $amountColumn = 'amount_dollar';
        }
        $walletPackages = WalletPackage::active()->where('for_new_user', 'no')->where($amountColumn, '>', 0)->get();
        if ($user->wallet_transaction_count == 0) {
            $newPackages = WalletPackage::active()->where('for_new_user', 'yes')->where($amountColumn, '>', 0)->get();
            $walletPackages = $newPackages->merge($walletPackages);
        }
        return $this->success(data: $walletPackages,message: 'Wallet Packages fetched successfully');
    }
    
    public function getAstros()
    {
        $astros = Astro::active()->select('id', 'first_name', 'middle_name', 'last_name', 'mobile')->get();
        return $this->success(data: $astros, message: 'Astros fetched successfully');
    }

    public function getLanguages()
    {
        $languages = Language::active()->get();
        return $this->success($languages, 'Languages fetched successfully!');
    }

    public function getSkills()
    {
        $skills = Skill::active()->get();
        return $this->success($skills, 'Skills fetched successfully!');
    }

    public function refreshDeviceId(Request $request)
    {
        $request->validate(
            [
                'user_id'   => 'nullable',
                'astro_id'  => 'nullable',
                'device_id' => 'required',
            ]
        );

        if (isset($request->user_id) && isset($request->device_id)) {
            $user            = User::where('id', $request->user_id)->first();
            $user->device_id = $request->device_id;
            $user->save();
        }

        if (isset($request->astro_id) && isset($request->device_id)) {
            $astro            = Astro::where('id', $request->astro_id)->first();
            $astro->device_id = $request->device_id;
            $astro->save();
        }

        return $this->success(message: 'Device Id updated successfully');

    }

    public function setAppVersions(Request $request)
    {
        $request->validate([
            'full_app_version_android_for_user'  => 'nullable',
            'app_version'                        => 'nullable',
            'full_app_version_ios_for_user'      => 'nullable',
            'ios_app_version'                    => 'nullable',
            'full_app_version_android_for_astro' => 'nullable',
            'full_app_version_ios_for_astro'     => 'nullable',
        ]);

        foreach ($request->all() as $key => $value) {
            BusinessSetting::updateOrCreate(
                [
                    'key' => $key,
                ],
                [
                    'value' => $value,
                ],
            );
        }

        return $this->success(message: "version updated successfully");
    }
}
