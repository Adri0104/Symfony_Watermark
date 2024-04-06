<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\ImageType;
use App\Service\PictureService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET', 'POST'])]
    public function index(Request $request, PictureService $pictureService, EntityManagerInterface $entityManager): Response
    {
        $image = new Image();

        $form = $this->createForm(ImageType::class, $image);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imagesFile = $form->get('imageFile')->getData();

            if ($imagesFile) {
                foreach ($imagesFile as $img) {
                    $folder = 'images';
                    $file = $pictureService->add($img, $folder, 300, 300);

                    $image = new Image();
                    $image->setImageFile($file);

                    $entityManager->persist($image);
                }
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('home/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
