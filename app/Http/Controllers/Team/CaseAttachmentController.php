<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\CaseAttachments;
use App\Models\Cases;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Exception;

class CaseAttachmentController extends Controller
{
    use ApiResponseTrait;

    private $ownerForeignKey;
    private $owner;

    public function __construct() {
        $owners = [
            \App\Models\Firm::class   => 'firm_id',
            \App\Models\Lawyer::class => 'lawyer_id',
            // ... add more role classes here
        ];

        $this->owner           = Auth::guard('team')->user()->owner; 
        $this->ownerForeignKey = $owners[get_class(Auth::guard('team')->user()->owner)] ?? throw new Exception('Owner not found'); 
    }

    /**
     * Remove the specified attachment from storage.
     *
     * @param  \App\Models\Cases  $case
     * @param  \App\Models\CaseAttachments  $attachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Cases $case, CaseAttachments $attachment)
    {
        // Ensure the authenticated lawyer is the owner of the case
        if ($case->{$this->ownerForeignKey} !==  $this->owner->id) {
            abort(403, 'Unauthorized action');
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
