<?php

namespace ARKEcosystem\CommonMark\Extensions\Highlighter;

use DomainException;

class CodeBlockHighlighter
{
    public function highlight(string $codeBlock, ?string $language = null)
    {
        if (str_contains($codeBlock, '<')) {
            preg_match('#<\s*?code\b[^>]*>(.*?)</code\b[^>]*>#s', $codeBlock, $matches);

            $codeBlock = $matches[1];
        } else {
            $codeBlock = trim(htmlspecialchars_decode(strip_tags($codeBlock)));
        }

        try {
            return vsprintf('<code class="hljs-copy language-%s">%s</code>', [$language, $codeBlock]);
        } catch (DomainException $e) {
            return $codeBlock;
        }
    }
}
