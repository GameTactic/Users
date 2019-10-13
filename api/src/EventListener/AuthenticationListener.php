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

namespace App\EventListener;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTAuthenticatedEvent;

final class AuthenticationListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * AuthenticationListener constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param JWTAuthenticatedEvent $event
     */
    public function onAuthenticated(JWTAuthenticatedEvent $event): void
    {
        ['username' => $username, 'region' => $region, 'uid' => $uid] = $event->getPayload();

        $user = $this->em
            ->getRepository(User::class)
            ->findOneBy(
                [
                    'userId'   => $uid,
                    'region'   => $region,
                    'username' => $username,
                ]
            );

        if (null !== $user) {
            return;
        }

        $user = new User($username, $uid, $region);
        $this->em->persist($user);
        $this->em->flush();
    }
}
