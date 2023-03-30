<?php

namespace App\Controller\Company;

use App\DTO\Company\CompanyVersionDTO;
use App\Entity\Common\Company;
use App\Repository\Common\CompanyRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CreateCompanyVersionController extends AbstractController
{
    #[Route(path: '/api/company/{idd}', name: 'create_company_version', methods: ['POST'])]
    #[ParamConverter('dto', class: CompanyVersionDTO::class)]
    public function __invoke(int $idd, CompanyVersionDTO $dto, CompanyRepository $companyRepository)
    {
        /** @var Company|null */
        $company = $companyRepository->findVersion($idd, new \DateTime());

        if (is_null($company)) {
            throw $this->createNotFoundException();
        }

        $newCompany = $companyRepository->createCompanyVersion(
            $company,
            $dto->getVersionFrom(),
            $dto->getVersionTo() ?? Company::getMaxPossibleDate(),
            $dto->getFullName(),
            $dto->getTaxId(),
            $dto->getRegistrationReasonCode(),
            $dto->getRegistrationNumber(),
            $dto->getRegistrationDate(),
        );

        return $this->json([]);
    }
}
