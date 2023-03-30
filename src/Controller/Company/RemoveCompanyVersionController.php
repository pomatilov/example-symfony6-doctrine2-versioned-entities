<?php

namespace App\Controller\Company;

use App\Entity\Common\Company;
use App\Repository\Common\CompanyRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RemoveCompanyVersionController extends AbstractController
{
    #[Route(
        path: '/api/company/{idd}/{timestamp}',
        name: 'remove_company_version',
        methods: ['DELETE'],
    )]
    public function __invoke(int $idd, int $timestamp, CompanyRepository $companyRepository)
    {
        $versionDate = \DateTime::createFromFormat('U', $timestamp);

        /** @var Company|null */
        $company = $companyRepository->findVersion($idd, $versionDate);

        if (is_null($company)) {
            throw $this->createNotFoundException();
        }

        $companyRepository->invalidateVersion($company);

        return $this->json([]);
    }
}
