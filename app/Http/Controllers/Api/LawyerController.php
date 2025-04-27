<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lawyer;
use App\Models\LawyerRatingView;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;

class LawyerController extends Controller
{    
    use ApiResponseTrait;
    
    /**
     * getLawyers
     * 
     * @param Request $request
     * @return void
     */
    public function getLawyers(Request $request){
        $query = LawyerRatingView::where('is_profile_completed', true);

        // Filter by city
        if ($request->has('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }
        
        // Filter by location
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        // Filter by availability time range
        if ($request->has('start_time') && $request->has('end_time')) {
            $query->where('availability_from', '<=', $request->start_time)
                  ->where('availability_to', '>=', $request->end_time);
        }

        // Filter by price range
        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Filter by years of experience
        if ($request->has('min_experience')) {
            $query->where('experience', '>=', $request->min_experience);
        }

        // Filter by specialization
        if ($request->has('specialization')) {
            $query->where('specialization', 'like', '%' . $request->specialization . '%');
        }

        // Filter by name
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $lawyers = $query->paginate(10)->toArray();
        return $this->successResponse('lawyers data', $lawyers);
    }
    /**
     * getReviews
     *
     * @param  mixed $laywerId
     * @return void
     */
    public function getReviews($laywerId){
        $reviews = Lawyer::find($laywerId)?->reviews()->paginate(10)->toArray();
        return $this->successResponse('laywer data', $reviews);
    }    
    /**
     * getLawyer
     *
     * @param  mixed $laywerId
     * @return void
     */
    public function getLawyer($laywerId){
        $lawyer = LawyerRatingView::find($laywerId);
        return $this->successResponse('laywer data', ['data' => $lawyer]);

    }
}
