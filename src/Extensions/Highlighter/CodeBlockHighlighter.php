<?php

namespace ARKEcosystem\CommonMark\Extensions\Highlighter;

use DomainException;

class CodeBlockHighlighter
{
    public function highlight(string $codeBlock, ?string $language = null)
    {
        $codeBlockWithoutTags = strip_tags($codeBlock);
        $contents             = trim(htmlspecialchars_decode($codeBlockWithoutTags));

        try {
            return vsprintf('<code class="hljs-copy language-%s">%s</code>', [$language, $contents]);
        } catch (DomainException $e) {
            return $codeBlock;
        }
    }
}
