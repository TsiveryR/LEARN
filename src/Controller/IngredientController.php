<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class IngredientController extends AbstractController
{
    /**
     * Ce fonction liste tous les ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'ingredient.index', methods:['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10 
        );
        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients'=> $ingredients
        ]);
    }

    #[Route('/ingredient/nouveau', 'ingredient.new', methods: ['GET', 'POST'])]
    /**
     * Ce controller affiche la création d'un nouveau ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function new (Request $request, EntityManagerInterface $manager): Response{
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingredient a été bien enregistrée !'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        return $this->render('pages/ingredient/new.html.twig',
        [
            'form' => $form->createView()
        ]);
    }
    
    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods:['GET', 'POST'])]
    // public function edit(
    //     Ingredient $ingredient,
    //     Request $request,
    //     EntityManagerInterface $manager
    // ):Response{
    //     $form = $this->createForm(IngredientType::class, $ingredient);

    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $ingredient = $form->getData();

    //         $manager->persist($ingredient);
    //         $manager->flush();
    //         $this->addFlash(
    //             'success',
    //             'Votre ingredient a été bien modifiée !'
    //         );

    //         return $this->redirectToRoute('ingredient.index');
    //     }
    //     return $this->render('pages/ingredient/edit.html.twig',[
    //         'form' => $form->createView()
    //     ]);
    // }
    /**
     * ce controller modifier un ingredient
     *
     * @param IngredientRepository $repository
     * @param integer $id
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function edit(
            IngredientRepository $repository, 
            int $id,
            Request $request,
            EntityManagerInterface $manager
        ):Response{
        $ingredient = $repository->findOneBy(['id'=>$id]);
        $form = $this->createForm(IngredientType::class, $ingredient);
            $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();

            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'Votre ingredient a été bien modifiée !'
            );

            return $this->redirectToRoute('ingredient.index');
        }

        $form->handleRequest($request);
        return $this->render('pages/ingredient/edit.html.twig',[
            'form' => $form->createView()
        ]);
    }
    
    #[Route('ingredient/suppression/{id}','ingredient.delete', methods:['GET'])]
    /**
     * suppression d'un ingredient
     *
     * @param IngredientRepository $repository
     * @param integer $id
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(
        IngredientRepository $repository,
        int $id,
        EntityManagerInterface $manager,
    ):Response{
        $ingredient = $repository->findOneBy(['id' => $id]);
        // if (!$ingredient) {
        //     $this->addFlash(
        //         'warning',
        //         'L\'ingredient en question n\'a pas été trouvé!'
        //     );
        // }
        $manager->remove($ingredient);
        $manager->flush();
        $this->addFlash(
            'success',
            'Votre ingredient a été bien supprimé !'
        );
        return $this->redirectToRoute('ingredient.index');
    }
}
