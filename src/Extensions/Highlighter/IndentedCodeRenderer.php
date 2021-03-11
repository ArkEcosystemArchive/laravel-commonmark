<?php

namespace ARKEcosystem\CommonMark\Extensions\Highlighter;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Block\Renderer\IndentedCodeRenderer as BaseIndentedCodeRenderer;
use League\CommonMark\ElementRendererInterface;

class IndentedCodeRenderer implements BlockRendererInterface
{
    /** @var \ARKEcosystem\CommonMark\Extensions\Highlighter\CodeBlockHighlighter */
    protected $highlighter;

    /** @var \League\CommonMark\Block\Renderer\IndentedCodeRenderer */
    protected $baseRenderer;

    public function __construct()
    {
        $this->highlighter  = new CodeBlockHighlighter();
        $this->baseRenderer = new BaseIndentedCodeRenderer();
    }

    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        $element = $this->baseRenderer->render($block, $htmlRenderer, $inTightList);
        dd($element);
        $element->setContents(
            $this->highlighter->highlight($element->getContents())
        );

        return $element;
    }
}
