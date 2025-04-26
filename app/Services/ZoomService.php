<?php

namespace App\Services;

use App\Models\AdminOption;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Http;

class ZoomService
{
    protected $clientId;
    protected $clientSecret;
    protected $accessToken;
    protected $refreshToken;
    protected $redirectURI;

    public function __construct()
    {
        $this->clientId     = env('ZOOM_CLIENT_ID');
        $this->clientSecret = env('ZOOM_CLIENT_SECRET');
        $this->redirectURI  = env('ZOOM_REDIRECT_URI');
        $this->accessToken  = AdminOption::get('zoom_access_token');
        $this->refreshToken = AdminOption::get('zoom_refresh_token');

        if (empty($this->accessToken) || empty($this->refreshToken())) {
            throw new Exception('zoom keys not generated');
        }
    }

    /**
     * Generate the URL to initiate the OAuth flow.
     * This method will generate the authorization URL where the superadmin
     * can authorize the app to access their Zoom account.
     */
    public function getOAuthUrl()
    {
        $url = 'https://zoom.us/oauth/authorize?' . http_build_query([
            'response_type' => 'code',
            'client_id'     => $this->clientId,
            'redirect_uri'  => $this->redirectURI,
        ]);

        return $url;
    }

    public function exchangeAuthorizationCodeForTokens($authCode)
    {

        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'authorization_code',
                'code'       => $authCode,
            ]);

        if (!$response->ok()) {
            throw new Exception($response->body());
        }

        return $response->json();
    }

    public function createMeeting(string $topic, $start_time, int $duration = 30): array
    {

        $payload = [
            'topic'      => $topic,
            'type'       => 2,
            'start_time' => Carbon::parse($start_time)->toIso8601String(),
            'duration'   => $duration,
            'timezone'   => 'UTC',
            'settings'   => [
                'join_before_host'       => true,
                'waiting_room'           => false,
                'meeting_authentication' => false,
                'host_video'             => true,
                'participant_video'      => true,
                'mute_upon_entry'        => true,
                'approval_type'          => 0,
                'auto_recording'         => 'none',
            ],
        ];

        $response = Http::withToken($this->accessToken)
            ->post('https://api.zoom.us/v2/users/me/meetings', $payload);

        // if token expired, refresh and retry once
        if ($response->status() === 401) {
            $this->refreshToken();
            $response = Http::withToken($this->accessToken)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post('https://api.zoom.us/v2/users/me/meetings', $payload);
        }

        if (!$response->created()) {
            throw new Exception('Zoom create meeting error: ' . $response->body());
        }

        return $response->json();
    }

    public function refreshToken()
    {

        $response = Http::withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post('https://zoom.us/oauth/token', [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $this->refreshToken,
            ]);

        if (!$response->ok()) {
            throw new Exception('Zoom token refresh failed: ' . $response->body());
        }

        $tokens = $response->json();
        $this->accessToken  = $tokens['access_token'];
        $this->refreshToken = $tokens['refresh_token'];

        AdminOption::set('zoom_access_token', $this->accessToken);
        AdminOption::set('zoom_refresh_token', $this->refreshToken);
    }
}
