<?php

use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use League\CommonMark\Util\Configuration;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Inline\Element\Text;

use League\CommonMark\Inline\Renderer\TextRenderer;
use function Spatie\Snapshots\assertMatchesSnapshot;
use ARKEcosystem\CommonMark\Extensions\Link\LinkRenderer;

it('should render internal/external links', function (string $url) {
    $subject = new LinkRenderer();
    $subject->setConfiguration(new Configuration());

    $environment = new Environment();
    $environment->addInlineRenderer(Text::class, new TextRenderer());

    assertMatchesSnapshot((string) $subject->render(new Link($url, "Label", "Title"), new HtmlRenderer($environment)));
})->with([
    'https://google.com',
    'https://ourapp.com',
    '#heading',
    '/path/segment',
]);
