<?php

/**
 *
 * GameTactic Users 2019 — NOTICE OF LICENSE
 *
 * This source file is released under GPLv3 license by copyright holders.
 * Please see LICENSE file for more specific licensing terms.
 * @copyright 2019-2019 (c) GameTactic
 * @author Niko Granö <niko@granö.fi>
 *
 */

namespace App\DataProvider;

use ApiPlatform\Core\DataProvider\CollectionDataProviderInterface;
use ApiPlatform\Core\DataProvider\RestrictedDataProviderInterface;
use ApiPlatform\Core\Exception\ResourceClassNotSupportedException;
use App\Entity\User;
use App\Security\Provider\JwtToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class UserCollectionDataProvider implements CollectionDataProviderInterface, RestrictedDataProviderInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;
    /**
     * @var TokenStorageInterface
     */
    private $token;

    /**
     * UserCollectionDataProvider constructor.
     *
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface  $token
     */
    public function __construct(EntityManagerInterface $em, TokenStorageInterface $token)
    {
        $this->em = $em;
        $this->token = $token;
    }

    /**
     * Retrieves a collection.
     *
     * @throws ResourceClassNotSupportedException
     *
     * @return iterable
     */
    public function getCollection(string $resourceClass, string $operationName = null)
    {
        $user = $this->token->getToken()->getUser();

        if (!($user instanceof JwtToken)) {
            throw new UnauthorizedHttpException('JWT Authentication required!');
        }

        if ('get' !== $operationName) {
            return [];
        }

        $user = $this->em
            ->getRepository(User::class)
            ->findOneBy(
                [
                    'userId'   => $user->getUid(),
                    'region'   => $user->getRegion(),
                    'username' => $user->getUsername(),
                ]
            );

        yield $user;
    }

    public function supports(string $resourceClass, string $operationName = null, array $context = []): bool
    {
        return User::class === $resourceClass;
    }
}
