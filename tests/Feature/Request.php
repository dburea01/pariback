<?php

namespace Tests\Feature;

trait Request
{
    public function getEndPoint(): string
    {
        return '/api/v1/';
    }

    /**
     * @return array<string>
     */
    public function setAuthorizationHeader(string $token): array
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '.$token,
        ];
    }

    /**
     * @return array<string>
     */
    public function setAcceptLanguageHeader(string $language): array
    {
        return [
            'Accept-Language' => $language,
        ];
    }
}
