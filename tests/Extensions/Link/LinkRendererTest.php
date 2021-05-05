<?php

use ARKEcosystem\CommonMark\Extensions\Link\LinkRenderer;
use League\CommonMark\Environment;
use League\CommonMark\HtmlRenderer;
use League\CommonMark\Inline\Element\Link;
use League\CommonMark\Util\Configuration;

use function Spatie\Snapshots\assertMatchesSnapshot;

it('should render internal/external links', function (string $url) {
    $subject = new LinkRenderer();
    $subject->setConfiguration(new Configuration());

    assertMatchesSnapshot((string) $subject->render(new Link($url), new HtmlRenderer(new Environment())));
})->with([
    'https://google.com',
    'https://ourapp.com',
    '#heading',
    '/path/segment',
]);
