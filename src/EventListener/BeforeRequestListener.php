<?php

namespace App\EventListener;

use App\Doctrine\ORM\Filter\VersionedEntityValidFilter;
use App\Doctrine\ORM\Filter\VersionedEntityVersionFilter;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class BeforeRequestListener
{
    public function __construct(
        private EntityManagerInterface $em,
        private LoggerInterface $logger,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $this->logger->debug(\sprintf('[%s] Calling %s', __CLASS__, __FUNCTION__));

        if ($event->isMainRequest() === false) {
            return;
        }

        $this->setGlobalFilters();

        $this->logger->debug(\sprintf('[%s] %s executed', __CLASS__, __FUNCTION__));
    }

    public function onConsoleCommand(ConsoleCommandEvent $event)
    {
        $this->logger->debug(\sprintf('[%s] Calling %s', __CLASS__, __FUNCTION__));

        $this->setGlobalFilters();

        $this->logger->debug(\sprintf('[%s] %s executed', __CLASS__, __FUNCTION__));
    }

    private function setGlobalFilters(): void
    {
        $versionedEntityValidFilter = $this->em->getFilters()
            ->enable(VersionedEntityValidFilter::FILTER_NAME);

        $versionedEntityVersionFilter = $this->em->getFilters()
            ->enable(VersionedEntityVersionFilter::FILTER_NAME);

        $versionedEntityVersionFilter->setParameter(
            VersionedEntityVersionFilter::VERSION_DATE_FORMAT_PARAMETER,
            VersionedEntityVersionFilter::DATE_FORMAT_PGSQL
        );

        $versionedEntityVersionFilter->setParameter(
            VersionedEntityVersionFilter::VERSION_DATE_PARAMETER,
            (new \DateTime())->format(VersionedEntityVersionFilter::DATE_FORMAT_PHP)
        );
    }
}
