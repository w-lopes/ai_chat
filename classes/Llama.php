<?php

namespace classes;

class Llama
{
    public static $history = [];

    public static function fileExists(): bool
    {
        return file_exists(Config::get('llamafile'));
    }

    public static function isRunning(): bool
    {
        $connection = @fsockopen(Config::get('llama_host'), Config::get('llama_port'));

        if ($connection) {
            fclose($connection);
            return true;
        } else {
            return false;
        }
    }

    public static function sendPrompt(string $prompt) {
        $url = 'http://' . Config::get('llama_host') . ':' . Config::get('llama_port') . '/v1/chat/completions';

        $data = [
            'messages' => array_merge(
                self::getDynamicSystemPrompt(),
                Config::get('system_messages'),
                self::$history,
            ),
            'response_format' => [
                'type' => 'json_object',
            ],
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            error('Error cURL: ' . curl_error($ch));
        }

        curl_close($ch);
        return json_decode(
            trim(
                preg_replace([
                    '/(<think>.*<\/think>)/s',
                    '/(<｜.*｜>)/m',
                    '/^```.*$/m'
                ], '', json_decode($response)->choices[0]->message->content
            )
        ), true);
    }

    public static function addToHistory(string $role, string $content): void
    {
        self::$history[] = [
            'role' => $role,
            'content' => $content,
        ];
        $n = Config::get('max_history', 500);
        self::$history = array_slice(self::$history, -$n, $n);
    }

    private static function getDynamicSystemPrompt(): array
    {
        $customCommands = array_map(function ($command) {
            unset($command['instance']);
            return $command;
        }, Command::$commands);

        $messages = [
            'You have to answer the user and determine if the message is a possible call for a custom command',
            'here is the list of the custom commands: ' . json_encode(array_values($customCommands)),
            'The response format must be a raw JSON object. Desired format: {
                "response", <generated response for the user message>,
                "is_custom_command": <bool>,
                "custom_command_class": <custom command name>
                "reason_for_custom_command": <a string explaining the why it was or not recognized as a custom command>
            }',
            'You must return only the json, no extras texts and markdowns'
        ];

        return array_map(fn ($message) => [
            'role' => 'system',
            'content' => $message,
        ], $messages);
    }
}
