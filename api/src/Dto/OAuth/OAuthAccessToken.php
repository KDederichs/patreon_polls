<?php

namespace App\Dto\OAuth;

use Symfony\Component\Serializer\Attribute\SerializedName;

class OAuthAccessToken
{
    #[SerializedName('access_token')]
    private ?string $accessToken = null;
    #[SerializedName('refresh_token')]
    private ?string $refreshToken = null;
    #[SerializedName('expires_in')]
    private ?int $expiresIn = null;
    #[SerializedName('scope')]
    private ?string $scope = null;
    #[SerializedName('token_type')]
    private ?string $tokenType = null;

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): OAuthAccessToken
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(?string $refreshToken): OAuthAccessToken
    {
        $this->refreshToken = $refreshToken;
        return $this;
    }

    public function getExpiresIn(): ?int
    {
        return $this->expiresIn;
    }

    public function setExpiresIn(?int $expiresIn): OAuthAccessToken
    {
        $this->expiresIn = $expiresIn;
        return $this;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): OAuthAccessToken
    {
        $this->scope = $scope;
        return $this;
    }

    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    public function setTokenType(?string $tokenType): OAuthAccessToken
    {
        $this->tokenType = $tokenType;
        return $this;
    }
}
