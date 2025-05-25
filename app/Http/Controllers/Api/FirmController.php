<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Firm;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class FirmController extends Controller
{
    use ApiResponseTrait;

    /**
     * getfirm
     * 
     * @param Request $request
     * @return void
     */
    public function getFirms(Request $request)
    {
        $query = Firm::where('is_profile_completed', true)->select('firms.*');

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        // Filter by location
        if ($request->filled('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by availability time range
        if ($request->filled('start_time') && $request->filled('end_time')) {
            $query->join('availabilities', 'availabilities.firm_id', '=', 'firms.id')
                ->whereBetween('availabilities.time', [$request->start_time, $request->end_time])
                ->groupBy('firm.id');
        }

        // Filter by price range
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by years of experience
        if ($request->filled('min_experience')) {
            $query->where('experience', '>=', $request->min_experience);
        }

        // Filter by specialization
        if ($request->filled('specialization')) {
            $query->where('specialization', 'like', '%' . $request->specialization . '%');
        }

        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $firm = $query->paginate(10);

        return $this->successResponse('firm data', [
            'firms'         => $firm->items(),
            'current_page'  => $firm->currentPage(),
            'last_page'     => $firm->lastPage(),
            'total'         => $firm->total(),
        ]);
    }
    /**
     * getfirm
     *
     * @param  mixed $laywerId
     * @return void
     */
    public function getFirm(Firm $firm)
    {
        $firm->makeHidden(['license_front', 'license_back', 'selfie']);
        return $this->successResponse('firm data', ['data' => $firm]);
    }
    /**
     * getAvailability
     *
     * @param  mixed $laywerId
     * @return void
     */
    public function getAvailability(Firm $firm)
    {  
        // Group availabilities by day and pluck times
        $availability = $firm->availabilities
            ->groupBy('day')
            ->mapWithKeys(fn($times, $day) => [$day => $times->pluck('time')]);
    
        return $this->successResponse('Availability', ['data' => $availability]);
    }
    
}
