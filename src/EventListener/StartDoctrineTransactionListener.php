<?php

namespace App\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class StartDoctrineTransactionListener
{
    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $em,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->logger->debug(\sprintf('[%s] Calling %s', __CLASS__, __FUNCTION__));

        $this->em->getConnection()->beginTransaction();

        $this->logger->debug(\sprintf('[%s] %s executed', __CLASS__, __FUNCTION__));
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $this->logger->debug(\sprintf('[%s] Calling %s', __CLASS__, __FUNCTION__));

        $this->em->flush();
        $this->em->getConnection()->commit();

        $this->logger->debug(\sprintf('[%s] %s executed', __CLASS__, __FUNCTION__));
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $this->logger->debug(\sprintf('[%s] Calling %s', __CLASS__, __FUNCTION__));
        $this->logger->error(\sprintf('[%s] Error: %s', __CLASS__, $event->getThrowable()?->getMessage()));


        $this->em->getConnection()->rollBack();

        $this->logger->debug(\sprintf('[%s] %s executed', __CLASS__, __FUNCTION__));
    }
}
