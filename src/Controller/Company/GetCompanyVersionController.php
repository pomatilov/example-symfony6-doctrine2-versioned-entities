<?php

namespace App\Controller\Company;

use App\DTO\Company\CompanyVersionDTO;
use App\Entity\Common\Company;
use App\Repository\Common\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GetCompanyVersionController extends AbstractController
{
    #[Route(
        path: '/api/company/{idd}/{timestamp}',
        name: 'get_company_version',
        methods: ['GET'],
        defaults: ['timestamp' => null],
    )]
    public function __invoke(int $idd, ?int $timestamp, CompanyRepository $companyRepository)
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

        $dto = (new CompanyVersionDTO())
            ->setVersionFrom($company->getVersionFrom())
            ->setVersionTo($company->getVersionTo())
            ->setFullName($company->getFullName())
            ->setTaxId($company->getTaxId())
            ->setRegistrationReasonCode($company->getRegistrationReasonCode())
            ->setRegistrationNumber($company->getRegistrationNumber())
            ->setRegistrationDate($company->getRegistrationDate())
        ;

        return $this->json($dto);
    }
}
