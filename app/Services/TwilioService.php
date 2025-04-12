<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );

        $this->from = config('services.twilio.from');
    }

    /**
     * Send an SMS message via Twilio
     *
     * @param  string  $to      Recipient’s phone number
     * @param  string  $message The SMS body
     * @return mixed
     */
    public function sendSms(string $to, string $message)
    {
        try {
            $result = $this->client->messages->create($to, [
                'from' => $this->from,
                'body' => $message
            ]);

            return $result;
        } catch (\Exception $e) {
            // You can choose to log it or handle it as needed
            Log::error('Twilio SMS failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Example of additional functionality:
     * Making voice calls, verifying phone numbers, etc.
     *
     * public function makeVoiceCall(string $to, string $url)
     * {
     *     try {
     *         return $this->client->calls->create(
     *             $to,
     *             $this->from,
     *             ['url' => $url]
     *         );
     *     } catch (\Exception $e) {
     *         Log::error('Twilio Call failed: ' . $e->getMessage());
     *         return false;
     *     }
     * }
     */
}
