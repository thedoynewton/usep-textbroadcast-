<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;
use Exception;

class MoviderService
{
    protected $client;
    protected $apiKey;
    protected $apiSecret;

    public function __construct()
    {
        // Initialize the Guzzle client with base URI and timeout settings
        $this->client = new Client([
            'base_uri' => 'https://api.movider.co/v1/', // Base URL for Movider API
            'timeout' => 5.0, // Set a higher timeout in case of delays
        ]);

        // Get the API key and secret from the environment variables
        $this->apiKey = env('MOVIDER_API_KEY');
        $this->apiSecret = env('MOVIDER_API_SECRET');
    }

    /**
     * Send bulk SMS to multiple recipients
     *
     * @param array $recipients
     * @param string $message
     * @return array
     */
    public function sendBulkSMS(array $recipients, string $message)
    {
        $url = 'sms'; // Movider API endpoint for SMS
        $params = [
            'form_params' => [
                'api_key' => $this->apiKey,
                'api_secret' => $this->apiSecret,
                'to' => implode(',', $recipients),
                'text' => $message,
                'from' => 'USeP' // Replace with your app name or sender name
            ]
        ];

        try {
            // Send the SMS request
            $response = $this->client->post($url, $params);
            $result = json_decode($response->getBody()->getContents(), true);

            // Log success message
            Log::info('Bulk SMS sent successfully to recipients.', ['response' => $result]);

            return $result;
        } catch (RequestException $e) {
            if ($e->hasResponse()) {
                // Capture the response and log the error message
                $errorMessage = $e->getResponse()->getBody()->getContents();
                Log::error('Failed to send bulk SMS: ' . $errorMessage);
            } else {
                Log::error('Failed to send bulk SMS: No response from the server.');
            }

            throw new Exception("Bulk SMS sending failed: " . $e->getMessage());
        } catch (Exception $e) {
            Log::error('An error occurred while sending bulk SMS: ' . $e->getMessage());
            throw new Exception("Bulk SMS sending failed: " . $e->getMessage());
        }
    }

    /**
     * Get SMS balance from Movider
     *
     * @return array
     */
    public function getBalance()
    {
        try {

            $response = $this->client->post('balance', [
                'form_params' => [
                    'api_key' => config('services.movider.api_key'),
                    'api_secret' => config('services.movider.api_secret'),
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            //Log::info('Movider Balance Response:', $data);

            $balance = $data['amount'] ?? 0;

            return ['balance' => $balance];
        } catch (Exception $e) {
            Log::error('Error fetching Movider balance: ' . $e->getMessage());
            return ['balance' => 0];
        }
    }

}
