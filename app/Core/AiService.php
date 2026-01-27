<?php

declare(strict_types=1);

namespace App\Core;

use RuntimeException;

final class AiService
{
    public function generateArticle(string $keyword): array
    {
        $apiKey = Config::get('OPENAI_API_KEY');
        if (!$apiKey) {
            throw new RuntimeException('Missing OpenAI API key.');
        }
        $prompt = "Generate a long-form finance article about {$keyword}. Cover key topics inspired by top search results. Include title, meta description, outline, and actionable steps.";
        $payload = [
            'model' => Config::get('OPENAI_MODEL', 'gpt-4o'),
            'messages' => [
                ['role' => 'system', 'content' => 'You are a finance expert creating comprehensive, SEO-focused articles.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.7,
        ];
        $response = $this->requestJson('https://api.openai.com/v1/chat/completions', $payload, [
            'Authorization: Bearer ' . $apiKey,
        ]);
        $content = $response['choices'][0]['message']['content'] ?? '';
        return [
            'title' => 'AI Generated: ' . ucfirst($keyword),
            'meta_description' => 'AI generated finance article for ' . $keyword,
            'content' => '<p>' . nl2br(htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8')) . '</p>',
            'featured_image' => 'https://images.unsplash.com/photo-1520607162513-77705c0f0d4a?auto=format&fit=crop&w=1200&q=80',
        ];
    }

    public function rewriteArticle(string $content): array
    {
        $apiKeys = [
            Config::get('PARAPHRASE_API_1_KEY'),
            Config::get('PARAPHRASE_API_2_KEY'),
            Config::get('PARAPHRASE_API_3_KEY'),
        ];
        if (empty(array_filter($apiKeys))) {
            throw new RuntimeException('Missing paraphrase API keys.');
        }
        $services = [
            ['url' => 'https://api.apyhub.com/utility/sharpapi-paraphrase-text', 'key' => $apiKeys[0] ?? ''],
            ['url' => 'https://api.meaningcloud.com/paraphrasing-2.0', 'key' => $apiKeys[1] ?? ''],
            ['url' => 'https://api.textgears.com/paraphrase', 'key' => $apiKeys[2] ?? ''],
        ];
        $rewrites = [];
        foreach ($services as $service) {
            if (!$service['key']) {
                continue;
            }
            $rewrites[] = $this->paraphraseViaService($service['url'], $service['key'], $content);
        }
        return array_filter($rewrites);
    }

    private function paraphraseViaService(string $url, string $key, string $content): string
    {
        $headers = [];
        if ($key) {
            $headers[] = 'Authorization: Bearer ' . $key;
        }
        $response = $this->requestJson($url, ['text' => $content], $headers);
        return $response['output'] ?? ($response['result'] ?? $content);
    }

    private function requestJson(string $url, array $payload, array $headers = []): array
    {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => array_merge(['Content-Type: application/json'], $headers),
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_TIMEOUT => 15,
        ]);
        $response = curl_exec($ch);
        if ($response === false) {
            throw new RuntimeException('AI request failed: ' . curl_error($ch));
        }
        curl_close($ch);
        return json_decode($response, true) ?? [];
    }
}
