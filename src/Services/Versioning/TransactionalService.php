<?php

namespace App\Services\Versioning;

use App\Entity\System\Transaction;
use App\Services\Versioning\Exception\NotInitializedTransactionException;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class TransactionalService
{
    private ?Transaction $transaction = null;

    public function __construct(
        private EntityManagerInterface $em,
        private RequestStack $requestStack,
        private LoggerInterface $versioningLogger,
    ) {
    }

    public function createTransaction(string $actionName, ?string $sessionId = null): void
    {
        $this->versioningLogger->info(\sprintf('[%s] Call %s for "%s"', __CLASS__, __FUNCTION__, $actionName));

        $newTransaction = (new Transaction())
            ->setSessionId($this->getSession()?->getId() ?? $sessionId)
            ->setName($actionName)
            ->setState(Transaction::STATE_WORKING)
        ;

        $this->em->persist($newTransaction);

        $this->transaction = $newTransaction;

        $this->versioningLogger->info(\sprintf('[%s] Transaction created for "%s"', __CLASS__, $actionName));
    }

    public function transactionSuccess(): void
    {
        $this->versioningLogger->info(\sprintf('[%s] Set transaction status "%s"', __CLASS__, Transaction::STATE_SUCCESS));

        if (is_null($this->transaction)) {
            $this->versioningLogger->info(\sprintf('[%s] Transaction doesn`t created:  Set status "%s" - skipped', __CLASS__, Transaction::STATE_SUCCESS));

            return;
        }

        $this->transaction->setState(Transaction::STATE_SUCCESS);

        $this->em->persist($this->transaction);

        $this->versioningLogger->info(\sprintf('[%s] Successfully set status "%s"', __CLASS__, Transaction::STATE_SUCCESS));
    }

    public function transactionFailed(): void
    {
        $this->versioningLogger->info(\sprintf('[%s] Set transaction status "%s"', __CLASS__, Transaction::STATE_FAILED));

        if (is_null($this->transaction)) {
            $this->versioningLogger->info(\sprintf('[%s] Transaction doesn`t created:  Set status "%s" - skipped', __CLASS__, Transaction::STATE_FAILED));

            return;
        }

        $this->transaction->setState(Transaction::STATE_FAILED);

        $this->em->persist($this->transaction);

        $this->versioningLogger->info(\sprintf('[%s] Successfully set status "%s"', __CLASS__, Transaction::STATE_FAILED));
    }

    public function getTransactionId()
    {
        if (isset($this->transaction)) {
            return $this->transaction->getId();
        }

        throw new NotInitializedTransactionException('Transaction wasn`t initialized');
    }

    private function getSession(): ?SessionInterface
    {
        return $this->requestStack?->getMainRequest()?->getSession() ?? null;
    }
}
