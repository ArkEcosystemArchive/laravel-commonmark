<?php

declare(strict_types=1);

namespace ARKEcosystem\CommonMark;

use League\Emoji\EmojiConverter;
use League\Emoji\Emojibase\EmojibaseDatasetInterface;
use League\Emoji\Emojibase\EmojibaseShortcodeInterface;

final class Emoji
{
    public static function convert(string $contents): string
    {
        $converter = EmojiConverter::create([
            'convertEmoticons'  => false,
            'exclude'           => [
                'shortcodes' => [],
            ],
            'locale'            => 'en',
            'native'            => false,
            'presentation'      => EmojibaseDatasetInterface::EMOJI,
            'preset'            => EmojibaseShortcodeInterface::DEFAULT_PRESETS,
        ]);

        return $converter->convert($contents);
    }
}
