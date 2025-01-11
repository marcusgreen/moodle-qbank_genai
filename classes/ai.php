<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

namespace qbank_genai;
use \moodle_exception;
/**
 * Class ai
 *
 * @package    qbank_genai
 * @copyright  2025 Marcus Green
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
/**
 * Summary of ai, make requests to the remote LLM service
 *
 */
class ai {

    public function perform_request(string $prompt, string $purpose = 'feedback', $context): string {
        // If local_ai_manager is not installed. Use the stand alone mode.
        $model = get_config('qbank_genai', 'model');
        $key = get_config('qbank_genai', 'key');

        $provider = get_config('qbank_genai', 'provider'); // OpenAI (default) or Azure

        if ($provider === 'Azure') {
            // If the provider is Azure, use the Azure API endpoint and Azure-specific HTTP header
            $url = get_config('qbank_genai', 'azure_api_endpoint'); // Use the Azure API endpoint from settings
            $authorization = "api-key: " . $key;
        } else {
            // If the provider is not Azure, use the OpenAI API URL and OpenAI style HTTP header
            $url = 'https://api.openai.com/v1/chat/completions';
            $authorization = "Authorization: Bearer " . $key;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', $authorization]);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $prompt);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2000);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;

    }

}
