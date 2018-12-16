<?php

declare(strict_types=1);

namespace App\Sms;

interface ScenesInterface
{
    public function template(): string;

    public function data($payload): array;

    public function content($payload): string;
}
