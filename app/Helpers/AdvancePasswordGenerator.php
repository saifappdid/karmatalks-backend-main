<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class AdvancePasswordGenerator
{
    public static function password($length = 32, $smallLetters = true, $capitalLetters = true, $numbers = true, $symbols = true, $spaces = false)
    {
        return (new Collection)
            ->when($smallLetters, fn ($c) => $c->merge([
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
                'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
                'w', 'x', 'y', 'z',
            ]))
            ->when($capitalLetters, fn ($c) => $c->merge([
                'A', 'B', 'C', 'D', 'E', 'F', 'G',
                'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
                'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            ]))
            ->when($numbers, fn ($c) => $c->merge([
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            ]))
            ->when($symbols, fn ($c) => $c->merge([
                '~', '!', '#', '$', '%', '^', '&', '*', '(', ')', '-',
                '_', '.', ',', '<', '>', '?', '/', '\\', '{', '}', '[',
                ']', '|', ':', ';',
            ]))
            ->when($spaces, fn ($c) => $c->merge([' ']))
            ->pipe(fn ($c) => Collection::times($length, fn () => $c[random_int(0, $c->count() - 1)]))
            ->implode('');
    }
}
