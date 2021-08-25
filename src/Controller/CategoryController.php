<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

class CategoryController extends AbstractController
{
    /**
     * @Route("/category", name="category_list")
     */
    public function viewAllCategory()
    {
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();

        return $this->render('category/index.html.twig',
            [
                'categories' => $categories
            ]);
    }
    /**
     * @Route("/category/detail/{id}", name="category_detail")
     */
    public function viewDetailCategory($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        if ($category == null) {
            $this->addFlash("Error", "Category ID is invalid");
            return $this->redirectToRoute("category_list");
        }
        return $this->render('category/detail.html.twig',
            [
                'category' => $category
            ]);
    }

    /**
     * @Route("/category/delete/{id}", name="category_delete")
     */
    public function deleteCategory($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        if ($category == null) {
            $this->addFlash("Error", "Category ID is invalid");
            return $this->redirectToRoute("category_list");
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($category);
        $manager->flush();

        $this->addFlash("Success", "Category has been deleted");
        return $this->redirectToRoute("category_list");
    }
    /**
     * @Route("/category/create", name="category_create")
     */
    public function createCategory(Request $request)
    {
        $category = new Category();
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //get image from upload file
            $image = $category->getImage();

            //create unique image name
            $fileName = md5(uniqid());
            $fileExtension = $image->guessExtension();
            $imageName = $fileName . '.' . $fileExtension;

            //move uploaded image to defined location
            try {
                $image->move(
                    $this->getParameter('category_image'), $imageName
                );
            } catch (FileException $e) {
                throwException($e);
            }

            //set imageName to database
            $category->setImage($imageName);

            $manager = $this->getDoctrine()
                ->getManager();
            $manager->persist($category);
            $manager->flush();
            $this->addFlash("Success", "Add category successfully !");
            return $this->redirectToRoute("category_list");
        }

        return $this->render("category/create.html.twig",
            [
                'form'=>$form->createView()
            ]);
    }

    /**
     * @Route("/category/update/{id}", name="category_update")
     */
    public function updateCategory(Request $request, $id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryFormType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['image']->getData();
            if ($uploadedFile != null) {
                $image = $category->getImage();

                //create unique image name
                $fileName = md5(uniqid());
                $fileExtension = $image->guessExtension();
                $imageName = $fileName . '.' . $fileExtension;

                //move uploaded image to defined location
                try {
                    $image->move(
                        $this->getParameter('category_image'), $imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }

                //set imageName to database
                $category->setImage($imageName);
            }
            //get image from upload file


            $manager = $this->getDoctrine()
                ->getManager();
            $manager->persist($category);
            $manager->flush();
            $this->addFlash("Success", "Update category successfully !");
            return $this->redirectToRoute("category_detail", ['id' => $id]);
        }

        return $this->render("category/update.html.twig",
            [
                'form'=>$form->createView()
            ]);
    }
}
