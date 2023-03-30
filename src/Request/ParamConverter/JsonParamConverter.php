<?php

namespace App\Request\ParamConverter;

use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class JsonParamConverter implements ParamConverterInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private LoggerInterface $converterLogger,
    ) {
    }

    public function supports(ParamConverter $configuration)
    {
        $this->converterLogger->debug(\sprintf('[%s] Calling %s', __CLASS__, __FUNCTION__));

        if (null === $configuration->getClass()) {
            return false;
        }

        return is_subclass_of($configuration->getClass(), ConvertableInterface::class);
    }

    public function apply(Request $request, ParamConverter $configuration)
    {
        $this->converterLogger->debug(\sprintf('[%s] Calling %s', __CLASS__, __FUNCTION__));

        $class = $configuration->getClass();

        try {
            $this->converterLogger->debug(\sprintf('[%s] Converting request body to "%s"', __CLASS__, $class));

            $object = $this->serializer->deserialize(
                $request->getContent(),
                $class,
                'json',
                [AbstractObjectNormalizer::DISABLE_TYPE_ENFORCEMENT => true],
            );
        } catch (\Throwable $e) {
            $this->converterLogger->error(\sprintf('[%s] Error "%s"', __CLASS__, $e->getMessage()));

            throw new NotFoundHttpException(\sprintf('Could not deserialize request content to object of type "%s"', $class));
        }

        $this->converterLogger->debug(\sprintf('[%s] Request body successfully converted to "%s"', __CLASS__, $class));

        // set the object as the request attribute with the given name
        // (this will later be an argument for the action)
        $request->attributes->set($configuration->getName(), $object);
    }
}
