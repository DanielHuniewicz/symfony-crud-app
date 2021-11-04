<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Controller\EmailController;
use App\Controller\AuthorizationController;


#[Route('/api')]
class ApiProductController extends AbstractApiProductController
{
    public function indexAction(ProductRepository $productRepository, Request $request): Response
    {
        if($request->get('sortField')){
            $sortField = $request->get('sortField');
        } else {
            $sortField = 'id';
        }
        
        $products = $productRepository->findAllWithSort($sortField);
        return $this->json($products);
    }

    public function showAction(Request $request): Response
    {
        $productId = $request->get('id');

        $product = $this->getDoctrine()->getRepository(Product::class)
            ->findOneBy(['id' => $productId]);

        if (!$product) {
            throw new NotFoundHttpException('Product not found');
        }

        return $this->respond($product);
    }

    public function newAction(Request $request, EmailController $email, AuthorizationController $authorization): Response
    {
        $apiKey = $request->headers->get('api-key');
        $authorization->userAuthorization($apiKey);

        $form->handleRequest($request);
        $form = $this->buildForm(ProductType::class);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Product $product */
        $product = $form->getData();

        $this->getDoctrine()->getManager()->persist($product);
        $this->getDoctrine()->getManager()->flush();

        $email->sendEmail(
            'fake@example.com',
            'product@app.com',
            'New product',
            '<p>Hey, you just added a new product</p>'
        );

        return $this->json($product);
    }

    public function deleteAction(Request $request, AuthorizationController $authorization): Response
    {
        $apiKey = $request->headers->get('api-key');
        $authorization->userAuthorization($apiKey);

        $productId = $request->get('id');

        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy([
            'id' => $productId,
        ]);

        if (!$product) {
            throw new NotFoundHttpException('Product not found');
        }

        $this->getDoctrine()->getManager()->remove($product);
        $this->getDoctrine()->getManager()->flush();

        return $this->respond(null);
    }

    public function updateAction(Request $request, AuthorizationController $authorization): Response
    {
        $apiKey = $request->headers->get('api-key');
        $authorization->userAuthorization($apiKey);
        
        $productId = $request->get('id');

        $product = $this->getDoctrine()->getRepository(Product::class)->findOneBy(
            ['id' => $productId]
        );

        if (!$product) {
            throw new NotFoundHttpException('Product not found');
        }

        $form = $this->buildForm(ProductType::class, $product, [
            'method' => $request->getMethod(),
        ]);

        $form->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            return $this->respond($form, Response::HTTP_BAD_REQUEST);
        }

        /** @var Product $product */
        $product = $form->getData();

        $this->getDoctrine()->getManager()->persist($product);
        $this->getDoctrine()->getManager()->flush();

        return $this->respond($product);
    }
}
