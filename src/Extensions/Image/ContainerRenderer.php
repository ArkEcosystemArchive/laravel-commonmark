<?php

namespace ARKEcosystem\CommonMark\Extensions\Image;

use League\CommonMark\HtmlElement;

final class ContainerRenderer
{
    public static function render($content, $title)
    {
        if (empty($title)) {
            return $content;
        }

        $container = new HtmlElement('div', ['class' => 'image-container'], '', true);
        $title     = new HtmlElement('span', ['class' => 'image-caption'], $title, true);

        return $container->setContents([$content, $title]);
    }
}
