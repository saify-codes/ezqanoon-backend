<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Availability;
use App\Models\Lawyer;
use App\Models\LawyerRatingView;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LawyerController extends Controller
{
    use ApiResponseTrait;

    public function getLawyers(Request $request)
    {
        $query = Lawyer::where('is_profile_completed', true)->select('lawyers.*');

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
            $query->join('availabilities', 'availabilities.lawyer_id', '=', 'lawyers.id')
                ->whereBetween('availabilities.time', [$request->start_time, $request->end_time])
                ->groupBy('lawyers.id');
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

        $lawyers = $query->paginate(10);

        return $this->successResponse('lawyers data', [
            'lawyers'       => $lawyers->items(),
            'current_page'  => $lawyers->currentPage(),
            'last_page'     => $lawyers->lastPage(),
            'total'         => $lawyers->total(),
        ]);
    }
   
    /**
     * getReviews
     *
     * @param  mixed $laywerId
     * @return void
     */
    // public function getReviews(Lawyer $lawyer)
    // {
    //     $reviews = Lawyer::find($laywerId)?->reviews()->paginate(10)->toArray();
    //     return $this->successResponse('laywer data', $reviews);
    // }
    
    public function getLawyer(Lawyer $lawyer)
    {
        $lawyer->makeHidden(['license_front', 'license_back', 'selfie']);
        return $this->successResponse('lawyer data', ['data' => $lawyer]);
    }

    public function getAvailability(Lawyer $lawyer)
    {   
        // Group availabilities by day and pluck times
        $availability = $lawyer->availabilities
            ->groupBy('day')
            ->mapWithKeys(fn($times, $day) => [$day => $times->pluck('time')]);
    
        return $this->successResponse('Availability', ['data' => $availability]);
    }
    
}
