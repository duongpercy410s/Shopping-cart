<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\OrderItem;
use App\Form\AddToCartType;
use App\Form\ProductFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;
use App\Manager\CartManager;
use App\Repository\ProductRepository;

class ProductController extends AbstractController
{
    /**
     * @Route("/product", name="product_list")
     */
    public function viewAllProduct()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        return $this->render('product/index.html.twig',
            [
                'products' => $products
            ]
        );
    }

    /**
     * @Route("/product/detail/{id}", name="product_detail")
     */
    public function ViewProductDetail(Request $request,CartManager $cartManager ,$id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $form = $this->createForm(AddToCartType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item = $form->getData();
            $item->setProduct($product);

            $cart = $cartManager->getCurrentCart();
            $cart
                ->addItem($item)
                ->getCreatedAt(new \DateTime());

            $cartManager->add($cart);
            $this->addFlash("Success", "Product added to cart successfully");
            return $this->redirectToRoute("product_detail", ['id' => $id]);
        }
        return $this->render(
            "product/detail.html.twig",
            [
                'product' => $product,
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/product/delete/{id}", name="product_delete")
     */
    public function deleteProduct($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($product == null) {
            $this->addFlash("Error", "Product ID is invalid");
            return $this->redirectToRoute("product_list");
        }
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($product);
        $manager->flush();

        $this->addFlash("Success", "Product has been deleted");
        return $this->redirectToRoute("product_list");
    }

    /**
     * @Route("/product/create", name="product_create")
     */
    public function createProduct(Request $request)
    {
        $product = new Product();
        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //get image from upload file
            $image = $product->getImage();

            //create unique image name
            $fileName = md5(uniqid());
            $fileExtension = $image->guessExtension();
            $imageName = $fileName . '.' . $fileExtension;

            //move uploaded image to defined location
            try {
                $image->move(
                    $this->getParameter('product_image'), $imageName
                );
            } catch (FileException $e) {
                throwException($e);
            }

            //set imageName to database
            $product->setImage($imageName);

            $manager = $this->getDoctrine()
                ->getManager();
            $manager->persist($product);
            $manager->flush();
            $this->addFlash("Success", "Add Product successfully !");
            return $this->redirectToRoute("product_list");
        }

        return $this->render(
            "product/create.html.twig",
            [
                "form" => $form->createView()
            ]
        );
    }

    /**
     * @Route("/product/update/{id}", name="product_update")
     */
    public function updateProduct(Request $request, $id)
    {
        $product = $this->getDoctrine()
            ->getRepository(Product::class)
            ->find($id);

        $form = $this->createForm(ProductFormType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $uploadedFile = $form['Image']->getData();
            if ($uploadedFile != null) {
                $image = $product->getImage();

                //create unique image name
                $fileName = md5(uniqid());
                $fileExtension = $image->guessExtension();
                $imageName = $fileName . '.' . $fileExtension;

                //move uploaded image to defined location
                try {
                    $image->move(
                        $this->getParameter('product_image'), $imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }

                //set imageName to database
                $product->setImage($imageName);
            }
            //get image from upload file


            $manager = $this->getDoctrine()
                ->getManager();
            $manager->persist($product);
            $manager->flush();
            $this->addFlash("Success", "Update product successfully !");
            return $this->redirectToRoute("product_detail", ['id' => $id]);
        }

        return $this->render(
            "product/update.html.twig",
            [
                "form" => $form->createView()
            ]
        );
    }
}
