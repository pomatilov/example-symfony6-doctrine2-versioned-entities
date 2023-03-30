<?php

namespace App\Controller\Company;

use App\DTO\Company\CompanyVersionDTO;
use App\Entity\Common\Company;
use App\Repository\Common\CompanyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class UpdateCompanyController extends AbstractController
{
    #[Route(
        path: '/api/company/{idd}',
        name: 'update_company',
        methods: ['PATCH'],
        defaults: ['timestamp' => null],
    )]
    #[ParamConverter('dto', class: CompanyVersionDTO::class)]
    public function __invoke(int $idd, CompanyVersionDTO $dto, CompanyRepository $companyRepository)
    {
        $versionDate = isset($timestamp)
            ? \DateTime::createFromFormat('U', $timestamp)
            : new \DateTime()
        ;

        /** @var Company|null */
        $company = $companyRepository->findVersion($idd, $versionDate);

        if (is_null($company)) {
            throw $this->createNotFoundException();
        }

        $companyRepository->updateCompany(
            $company,
            $dto->getFullName(),
            $dto->getTaxId(),
            $dto->getRegistrationReasonCode(),
            $dto->getRegistrationNumber(),
            $dto->getRegistrationDate(),
        );

        return $this->json([]);
    }
}
