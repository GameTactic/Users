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

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ApiResource(
 *     attributes={"security"="is_granted('IS_AUTHENTICATED_FULLY')"},
 *     normalizationContext={"groups"={"user_read"}},
 *     denormalizationContext={"groups"={"user_write"}},
 *     collectionOperations={
 *         "get"={},
 *     },
 *     itemOperations={
 *         "get"={},
 *     },
 * )
 */
final class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Groups({"user_read"})
     */
    private $userId;

    /**
     * @ORM\Column(type="string")
     * @Groups({"user_read"})
     */
    private $username;

    /**
     * @ORM\Column(type="string")
     * @Groups({"user_read"})
     */
    private $region;

    /**
     * User constructor.
     */
    public function __construct(string $username, string $userId, string $region)
    {
        $this->username = $username;
        $this->userId = $userId;
        $this->region = $region;
    }

    public function setUserId(string $userId): void
    {
        $this->userId = $userId;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function setRegion(string $region): void
    {
        if (!\in_array($region, ['eu', 'na', 'asia', 'ru'], true)) {
            throw new \InvalidArgumentException('Not recognized region!');
        }

        $this->region = $region;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getRegion(): string
    {
        return $this->region;
    }
}
