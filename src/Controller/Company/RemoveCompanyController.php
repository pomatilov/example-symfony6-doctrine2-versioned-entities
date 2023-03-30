<?php

namespace App\Controller\Company;

use App\Entity\Common\Company;
use App\Repository\Common\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class RemoveCompanyController extends AbstractController
{
    #[Route(
        path: '/api/company/{idd}',
        name: 'remove_company',
        methods: ['DELETE'],
    )]
    public function __invoke(int $idd, CompanyRepository $companyRepository)
    {
        /** @var array<Company> */
        $companyVersions = $companyRepository->findAllVersions($idd);

        if (count($companyVersions) === 0) {
            throw $this->createNotFoundException();
        }

        foreach ($companyVersions as $companyVersion) {
            $companyRepository->invalidateVersion($companyVersion);
        }

        return $this->json([]);
    }
}
