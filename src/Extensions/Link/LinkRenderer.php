<?php

namespace ARKEcosystem\CommonMark\Extensions\Link;

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

        if (str_starts_with($attrs['href'], config('app.url'))) {
            return new HtmlElement('a', $attrs, $htmlRenderer->renderInlines($inline->children()));
        }

        $text = $attrs['title'] ?? $attrs['href'];

        // If the child is not as URL we can use it as the text for the link
        $children = trim($htmlRenderer->renderInlines($inline->children()));

        if (! filter_var($children, FILTER_VALIDATE_URL)) {
            $text = $children;
        }

        return view(config('markdown.link_renderer_view', 'ark::external-link'), array_merge(
            config('markdown.link_renderer_view_attributes', ['inline' => true]),
            [
                'attributes' => new ComponentAttributeBag([]),
                'text' => $text,
                'url'  => $attrs['href'],
            ]
        ))->render();
    }

    public function setConfiguration(ConfigurationInterface $configuration)
    {
        $this->config = $configuration;
    }
}
