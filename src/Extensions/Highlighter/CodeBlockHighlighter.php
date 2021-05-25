<?php

namespace ARKEcosystem\CommonMark\Extensions\Highlighter;

use DomainException;

class CodeBlockHighlighter
{
    public function highlight(string $codeBlock, ?string $language = null)
    {
        if ($language === "xml") {
            return $codeBlock;
        }

        try {
            return vsprintf('<code class="hljs-copy language-%s">%s</code>', [
                $language,
                trim(htmlspecialchars_decode(strip_tags($codeBlock))),
            ]);
        } catch (DomainException $e) {
            return $codeBlock;
        }
    }
}
