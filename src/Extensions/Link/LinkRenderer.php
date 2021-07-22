<?php

namespace ARKEcosystem\CommonMark\Extensions\Link;

use Illuminate\Support\Arr;
use Illuminate\View\ComponentAttributeBag;
use League\CommonMark\ElementRendererInterface;
use League\CommonMark\HtmlElement;
use League\CommonMark\Inline\Element\AbstractInline;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Renderer\InlineRendererInterface;
use League\CommonMark\Util\ConfigurationAwareInterface;
use League\CommonMark\Util\ConfigurationInterface;
use League\CommonMark\Util\RegexHelper;

final class LinkRenderer implements InlineRendererInterface, ConfigurationAwareInterface
{
    /**
     * @var ConfigurationInterface
     */
    protected $config;

    /**
     * @param Link                     $inline
     * @param ElementRendererInterface $htmlRenderer
     *
     * @return HtmlElement
     */
    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer)
    {
        if (! ($inline instanceof Link)) {
            throw new \InvalidArgumentException('Incompatible inline type: '.\get_class($inline));
        }

        $attrs = $inline->getData('attributes', []);

        $forbidUnsafeLinks = ! $this->config->get('allow_unsafe_links');
        if (! ($forbidUnsafeLinks && RegexHelper::isLinkPotentiallyUnsafe($inline->getUrl()))) {
            $attrs['href'] = $inline->getUrl();
        }

        if (isset($inline->data['title'])) {
            $attrs['title'] = $inline->data['title'];
        }

        if (isset($attrs['target']) && $attrs['target'] === '_blank' && ! isset($attrs['rel'])) {
            $attrs['rel'] = 'noopener nofollow noreferrer';
        }

        $attrs = array_merge(Arr::only($attrs, ['href', 'id', 'class', 'name', 'title']), config('markdown.link_attributes', []));
        
        if (! $this->isInternalLink($attrs['href'])) {
            $attrs['target'] = '_blank';
            $attrs['data-external'] = 'true';
        }
        
        return new HtmlElement('a', $attrs, $htmlRenderer->renderInlines($inline->children()));
    }

    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->config = $configuration;
    }

    private function isInternalLink(string $url): bool
    {
        if (str_starts_with($url, config('app.url'))) {
            return true;
        }

        // Anchors
        if (str_starts_with($url, '#')) {
            return true;
        }

        // Relative links, but not protocol relative
        if (str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return true;
        }

        return false;
    }
}
