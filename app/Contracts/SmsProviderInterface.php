<?php

namespace App\Contracts;

interface SmsProviderInterface
{
    public function send(string $to, string $message, array $options = []): array;
}
