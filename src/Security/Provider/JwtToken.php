<?php

/**
 *
 * GameTactic Users 2020 — NOTICE OF LICENSE
 *
 * This source file is released under GPLv3 license by copyright holders.
 * Please see LICENSE file for more specific licensing terms.
 * @copyright 2019-2020 (c) GameTactic
 * @author Niko Granö <niko@granö.fi>
 *
 */

namespace App\Security\Provider;

use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;

final class JwtToken implements JWTUserInterface
{
    /**
     * @var string
     */
    private $username;
    /**
     * @var string
     */
    private $region;
    /**
     * @var string
     */
    private $uid;

    public function __construct(string $username, string $region, string $uid)
    {
        $this->username = $username;
        $this->region = $region;
        $this->uid = $uid;
    }

    /**
     * Creates a new instance from a given JWT payload.
     *
     * @param string $username
     */
    public static function createFromPayload($username, array $payload): JWTUserInterface
    {
        return new self($username, $payload['region'], $payload['uid']);
    }

    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return ['ROLE_USER'];
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array The user roles
     */
    public function getRoles(): array
    {
        return [];
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string|null The encoded password if any
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials(): void
    {
        // Do nothing.
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public function getUid(): string
    {
        return $this->uid;
    }
}
