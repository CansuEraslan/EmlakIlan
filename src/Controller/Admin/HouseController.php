<?php

namespace App\Controller\Admin;

use App\Entity\House;
use App\Form\HouseType;
use App\Repository\HouseRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/house")
 */
class HouseController extends AbstractController
{
    /**
     * @Route("/", name="admin_house_index", methods={"GET"})
     */
    public function index(HouseRepository $houseRepository): Response
    {
        $houses=$houseRepository->getAllHouses();
        return $this->render('admin/house/index.html.twig', [
            'houses' => $houses,
        ]);
    }

    /**
     * @Route("/new", name="admin_house_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $house = new House();
        $form = $this->createForm(HouseType::class, $house);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            /*******************file upload*****************/
            $file=$form['image']->getData();
            if($file){
                $fileName=$this->generatedUniqueFileName(). '.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('image_directory'),
                        $fileName
                    );
                }catch(FileException $e){

                }
                $house->setImage($fileName);
            }
            /*************file upload**********/
            $entityManager->persist($house);
            $entityManager->flush();

            return $this->redirectToRoute('admin_house_index');
        }

        return $this->render('admin/house/new.html.twig', [
            'house' => $house,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="admin_house_show", methods={"GET"})
     */
    public function show(House $house): Response
    {
        return $this->render('admin/house/show.html.twig', [
            'house' => $house,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="admin_house_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, House $house): Response
    {
        $form = $this->createForm(HouseType::class, $house);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file=$form['image']->getData();
            if($file){
                $fileName=$this->generatedUniqueFileName(). '.'.$file->guessExtension();
                try{
                    $file->move(
                        $this->getParameter('image_directory'),
                        $fileName
                    );
                }catch(FileException $e){

                }
                $house->setImage($fileName);
            }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('admin_house_index');
        }

        return $this->render('admin/house/edit.html.twig', [
            'house' => $house,
            'form' => $form->createView(),
        ]);
    }
    private function generatedUniqueFileName(){
        return md5(uniqid());
    }

    /**
     * @Route("/{id}", name="admin_house_delete", methods={"DELETE"})
     */
    public function delete(Request $request, House $house): Response
    {
        if ($this->isCsrfTokenValid('delete'.$house->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($house);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_house_index');
    }
}
