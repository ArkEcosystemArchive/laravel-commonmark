<?php

namespace ARKEcosystem\CommonMark\Extensions\Highlighter;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\Block\Element\FencedCode;
use League\CommonMark\Block\Renderer\BlockRendererInterface;
use League\CommonMark\Block\Renderer\FencedCodeRenderer as BaseFencedCodeRenderer;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Util\Xml;

class FencedCodeRenderer implements BlockRendererInterface
{
    /** @var \ARKEcosystem\CommonMark\Extensions\Highlighter\CodeBlockHighlighter */
    protected $highlighter;

    /** @var \League\CommonMark\Block\Renderer\FencedCodeRenderer */
    protected $baseRenderer;

    public function __construct()
    {
        $this->highlighter  = new CodeBlockHighlighter();
        $this->baseRenderer = new BaseFencedCodeRenderer();
    }

    public function render(AbstractBlock $block, ElementRendererInterface $htmlRenderer, $inTightList = false)
    {
        $element = $this->baseRenderer->render($block, $htmlRenderer, $inTightList);

        $this->configureLineNumbers($element);

        $element->setContents(
            $this->highlighter->highlight(
                $element->getContents(),
                $this->getSpecifiedLanguage($block)
            )
        );

        $container = new HtmlElement('div', ['class' => 'p-4 mb-6 rounded-lg bg-theme-secondary-800 overflow-x-auto']);
        $container->setContents($element);

        return $container;
    }

    protected function configureLineNumbers(HtmlElement $element): void
    {
        $codeBlockWithoutTags = strip_tags($element->getContents());
        $contents             = trim(htmlspecialchars_decode($codeBlockWithoutTags));

        if (count(explode("\n", $contents)) === 1) {
            $element->setAttribute('class', 'hljs');
        } else {
            $element->setAttribute('class', 'hljs line-numbers');
        }
    }

    protected function getSpecifiedLanguage(FencedCode $block): ?string
    {
        $infoWords = $block->getInfoWords();

        if (empty($infoWords) || empty($infoWords[0])) {
            return null;
        }

        return Xml::escape($infoWords[0]);
    }
}
