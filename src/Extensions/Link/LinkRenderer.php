<?php

declare(strict_types=1);

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

    public function render(AbstractInline $inline, ElementRendererInterface $htmlRenderer): HtmlElement | string
    {
        if (! ($inline instanceof Link)) {
            throw new \InvalidArgumentException('Incompatible inline type: '.\get_class($inline));
        }

        $attrs = $inline->getData('attributes', []);

        $forbidUnsafeLinks = $this->config->get('allow_unsafe_links', true) !== true;
        if (! ($forbidUnsafeLinks && RegexHelper::isLinkPotentiallyUnsafe($inline->getUrl()))) {
            $attrs['href'] = $inline->getUrl();
        }

        /* @phpstan-ignore-next-line */
        if (isset($inline->data['title'])) {
            $attrs['title'] = $inline->data['title'];
        }

        /* @phpstan-ignore-next-line */
        if (isset($attrs['target']) && $attrs['target'] === '_blank' && ! isset($attrs['rel'])) {
            $attrs['rel'] = 'noopener nofollow noreferrer';
        }

        if ($this->isInternalLink($attrs['href'])) {
            $attrs = array_merge(Arr::only($attrs, ['href', 'id', 'class', 'name', 'title']), config('markdown.link_attributes', []));

            return new HtmlElement('a', $attrs, $htmlRenderer->renderInlines($inline->children()));
        }

        $text = $attrs['title'] ?? $attrs['href'];

        // If the child is not as URL we can use it as the text for the link
        $children = trim($htmlRenderer->renderInlines($inline->children()));

        if (filter_var($children, FILTER_VALIDATE_URL) === false) {
            $text = $children;
        }

        return view(config('markdown.link_renderer_view', 'ark::external-link'), array_merge(
            config('markdown.link_renderer_view_attributes', ['inline' => true]),
            [
                'attributes' => new ComponentAttributeBag([]),
                'text'       => $text,
                'url'        => $attrs['href'],
            ]
        ))->__toString();
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
