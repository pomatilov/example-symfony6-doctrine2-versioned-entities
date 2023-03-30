<?php

namespace App\Controller\Company;

use App\DTO\Company\CompanyDTO;
use App\Repository\Common\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CreateCompanyController extends AbstractController
{
    #[Route(path: '/api/company', name: 'create_company', methods: ['POST'])]
    #[ParamConverter('dto', class: CompanyDTO::class)]
    public function __invoke(CompanyDTO $dto, EntityManagerInterface $em, CompanyRepository $companyRepository)
    {
        $newCompany = $companyRepository->createCompany(
            $dto->getFullName(),
            $dto->getTaxId(),
            $dto->getRegistrationReasonCode(),
            $dto->getRegistrationNumber(),
            $dto->getRegistrationDate(),
        );

        $em->flush();

        return $this->json(['idd' => $companyRepository->find($newCompany->getId())?->getIdd()]);
    }
}
