<?php

declare(strict_types=1);

namespace App\Core;

final class AiService
{
    public function generateArticle(string $keyword): array
    {
        $title = sprintf('Mastering %s: A Modern Finance Guide', ucfirst($keyword));
        $content = '<p>This long-form guide explains ' . $keyword . ' with actionable steps, case studies, and frameworks for smarter money decisions.</p>';
        $content .= '<p>Explore fundamentals, advanced strategies, and practical checklists to implement today.</p>';
        return [
            'title' => $title,
            'meta_description' => 'Learn the key strategies for ' . $keyword . ' with a premium step-by-step finance guide.',
            'content' => $content,
        ];
    }

    public function rewriteArticle(string $content): array
    {
        $rewrites = [];
        for ($i = 1; $i <= 4; $i++) {
            $rewrites[] = sprintf('Rewrite %d: %s', $i, $content);
        }
        return $rewrites;
    }
}
