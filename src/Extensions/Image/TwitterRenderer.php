<?php

namespace ARKEcosystem\CommonMark\Extensions\Image;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

final class TwitterRenderer
{
    public static function render(MediaUrl $url)
    {
        $url = 'https://twitter.com/'.$url->getId();

        return Cache::rememberForever(md5($url), fn () => Http::get('https://publish.twitter.com/oembed', [
            'url'         => $url,
            'hide_thread' => 1,
            'hide_media'  => 0,
            'omit_script' => true,
            'dnt'         => true,
            'limit'       => 20,
            'chrome'      => 'nofooter',
        ])->json()['html']);
    }
}
