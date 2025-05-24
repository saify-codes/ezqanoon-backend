<?php

namespace App\Http\Controllers\Lawyer;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientAttachments;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;

class ClientAttachmentController extends Controller
{
    use ApiResponseTrait;

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
        if ($client->lawyer_id !== Auth::user()->id) {
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
