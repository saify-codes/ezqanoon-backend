<?php

namespace App\Http\Controllers\Team;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientAttachments;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Exception;



class ClientAttachmentController extends Controller
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
     * @param  \App\Models\Client  $case
     * @param  \App\Models\CaseAttachments  $attachment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Client $client, ClientAttachments $attachment)
    {
        // dd($client, $attachment);
        // Ensure the authenticated lawyer is the owner of the case
        if ($client->{$this->ownerForeignKey} !== $this->owner->id) {
            abort(403, 'Unauthorized action.');
        }

        // Check if the attachment belongs to the provided case
        if ($attachment->client_id !== $client->id) {
            abort(404);
        }

        // Delete the attachment
        $attachment->delete();
        
        // Redirect back to the case details with a success message
        return $this->successResponse('AppointmentAttachment deleted');
    }
}
