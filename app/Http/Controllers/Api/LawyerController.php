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
     * @return void
     */
    public function getLawyers(){
        $lawyers = LawyerRatingView::where('is_profile_completed', true)->paginate(10)->toArray();
        return $this->successResponse('laywers data', $lawyers);

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
