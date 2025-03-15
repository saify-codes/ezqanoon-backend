<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\CaseAttachments;
use App\Models\Cases;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CaseAttachmentController extends Controller
{
    use ApiResponseTrait;

    /**
     * Remove the specified attachment from storage.
     *
     * @param  \App\Models\Cases  $case
     * @param  \App\Models\CaseAttachments  $attachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cases $case, CaseAttachments $attachment)
    {
        // dd($case, $attachment);
        // Ensure the authenticated lawyer is the owner of the case
        if ($case->lawyer_id !== Auth::user()->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the attachment belongs to the provided case
        if ($attachment->case_id !== $case->id) {
            abort(404);
        }

        // Delete the attachment
        $attachment->delete();
        
        // Redirect back to the case details with a success message
        return $this->successResponse('Attachment deleted');
    }
}
