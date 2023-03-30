<?php

namespace App\EventListener;

use App\Services\Versioning\TransactionalService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class SetTransactionIdentifierListener
{
    private $supportedPaths = [
        'api'
    ];

    public function __construct(
        private TransactionalService $transactionalService,
        private LoggerInterface $logger,
        private EntityManagerInterface $em,
    ) {
    }

    public function onKernelController(ControllerEvent $event): void
    {
        $this->logger->debug(\sprintf('[%s] Calling %s', __CLASS__, __FUNCTION__));

        if ($event->isMainRequest() === false || $this->supports($event->getRequest()) === false) {
            return;
        }

        $request = $event->getRequest();

        $routeName = $request->get('_route') ?? 'unknown_route_name';

        $this->transactionalService->createTransaction($routeName);

        $this->logger->debug(\sprintf('[%s] %s executed', __CLASS__, __FUNCTION__));
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $this->logger->debug(\sprintf('[%s] Calling %s', __CLASS__, __FUNCTION__));

        if ($event->isMainRequest() === false || $this->supports($event->getRequest()) === false) {
            return;
        }

        $this->transactionalService->transactionSuccess();

        $this->logger->debug(\sprintf('[%s] %s executed', __CLASS__, __FUNCTION__));
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $this->logger->debug(\sprintf('[%s] Calling %s', __CLASS__, __FUNCTION__));
        $this->logger->error(\sprintf('[%s] Error: %s', __CLASS__, $event->getThrowable()?->getMessage()));

        if ($event->isMainRequest() === false || $this->supports($event->getRequest()) === false) {
            return;
        }

        $this->transactionalService->transactionFailed();

        $this->logger->debug(\sprintf('[%s] %s executed', __CLASS__, __FUNCTION__));
    }

    private function supports(Request $request): bool
    {
        $uri = ltrim($request->getRequestUri(), '/');

        $this->logger->debug(\sprintf('[%s] Check supports uri "%s"', __CLASS__, $uri));

        foreach ($this->supportedPaths as $supportedPath) {
            if (strpos($uri, $supportedPath) === 0) {
                return true;
            }
        }

        return false;
    }

    // public function onConsoleCommand(ConsoleCommandEvent $event)
    // {
    //     $this->logger->debug(\sprintf('[%s] Calling %s', __CLASS__, __FUNCTION__));

    //     $commandName = $event->getCommand()?->getName() ?? 'command_event';

    //     if (CommandListenerHelper::supportsCommandName($commandName) === false) {
    //         return;
    //     }

    //     $this->transactionalService->create($commandName, $commandName);

    //     $this->logger->debug(\sprintf('[%s] %s executed', __CLASS__, __FUNCTION__));
    // }

    // public function onConsoleTerminate(ConsoleTerminateEvent $event)
    // {
    //     $this->logger->debug(\sprintf('[%s] Calling %s', __CLASS__, __FUNCTION__));

    //     $commandName = $event->getCommand()?->getName() ?? 'command_event';

    //     if (CommandListenerHelper::supportsCommandName($commandName) === false) {
    //         return;
    //     }

    //     $this->transactionalService->success();

    //     $this->logger->debug(\sprintf('[%s] %s executed', __CLASS__, __FUNCTION__));
    // }

    // /**
    //  * Handle exceptions thrown during the execution of a command.
    //  */
    // public function onConsoleError(ConsoleErrorEvent $event)
    // {
    //     $this->logger->debug(\sprintf('[%s] Calling %s', __CLASS__, __FUNCTION__));

    //     $commandName = $event->getCommand()?->getName() ?? 'command_event';

    //     if (CommandListenerHelper::supportsCommandName($commandName) === false) {
    //         return;
    //     }

    //     $this->transactionalService->failed();

    //     $this->logger->debug(\sprintf('[%s] %s executed', __CLASS__, __FUNCTION__));
    // }
}
