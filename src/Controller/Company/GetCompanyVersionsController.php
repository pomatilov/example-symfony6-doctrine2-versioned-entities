<?php

namespace App\Controller\Company;

use App\DTO\Company\CompanyVersionDTO;
use App\Entity\Common\Company;
use App\Repository\Common\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class GetCompanyVersionsController extends AbstractController
{
    #[Route(
        path: '/api/company_versions/{idd}',
        name: 'get_company_versions',
        methods: ['GET'],
    )]
    public function __invoke(int $idd, CompanyRepository $companyRepository)
    {
        /** @var array<Company> */
        $companyVersions = $companyRepository->findAllVersions($idd);

        if (count($companyVersions) === 0) {
            throw $this->createNotFoundException();
        }

        $resultList = [];

        foreach ($companyVersions as $companyVersion) {
            $resultList[] = (new CompanyVersionDTO())
                ->setVersionFrom($companyVersion->getVersionFrom())
                ->setVersionTo($companyVersion->getVersionTo())
                ->setFullName($companyVersion->getFullName())
                ->setTaxId($companyVersion->getTaxId())
                ->setRegistrationReasonCode($companyVersion->getRegistrationReasonCode())
                ->setRegistrationNumber($companyVersion->getRegistrationNumber())
                ->setRegistrationDate($companyVersion->getRegistrationDate())
            ;
        }

        return $this->json($resultList);
    }
}
