<?php

namespace App\Controller;

use App\Entity\Restaurant;
use App\Entity\Test;
use App\Form\RestaurantType;
use App\Repository\RestaurantRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/restaurant")
 */
class RestaurantController extends AbstractController
{
    /**
     * @Route("/api/show", name="restaurant_api_show")
     */
    public function showAll(){
        $restaurant = $this->getDoctrine()->getRepository(Restaurant::class)->findAll();
        $serializer = new Serializer([new DateTimeNormalizer(), new ObjectNormalizer()]);
        $formatted = $serializer->normalize($restaurant);
        return new JsonResponse($formatted);
    }
    /**
     * @Route("/", name="restaurant_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $restaurant = $this->getDoctrine()
            ->getRepository(Restaurant::class)
            ->findAll();
        $restaurants = $paginator->paginate($restaurant,$request->query->getInt('page', 1),5);
        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }
    /**
     * @Route("/front", name="restaurant_index_front", methods={"GET"})
     */
    public function indexFront(Request $request, PaginatorInterface $paginator): Response
    {
        $restaurant = $this->getDoctrine()
            ->getRepository(Restaurant::class)
            ->findAll();
        $restaurants = $paginator->paginate($restaurant,$request->query->getInt('page', 1),10);
        return $this->render('restaurant/front_index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }

    /**
     * @Route("/new", name="restaurant_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $restaurant = new Restaurant();
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($restaurant);
            $entityManager->flush();

            return $this->redirectToRoute('restaurant_index');
        }

        return $this->render('restaurant/new.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="restaurant_show", methods={"GET"})
     */
    public function show(Restaurant $restaurant): Response
    {
        return $this->render('restaurant/show.html.twig', [
            'restaurant' => $restaurant,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="restaurant_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Restaurant $restaurant): Response
    {
        $form = $this->createForm(RestaurantType::class, $restaurant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('restaurant_index');
        }

        return $this->render('restaurant/edit.html.twig', [
            'restaurant' => $restaurant,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="restaurant_delete", methods={"POST"})
     */
    public function delete(Request $request, Restaurant $restaurant): Response
    {
        if ($this->isCsrfTokenValid('delete'.$restaurant->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($restaurant);
            $entityManager->flush();
        }

        return $this->redirectToRoute('restaurant_index');
    }

    /**
     * @Route("/index/pdf", name="restaurant_pdf", methods={"GET"})
     */
    public function pdfGenerator():Response{
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $restaurants=  $this->getDoctrine()
            ->getRepository(Restaurant::class)
            ->findAll();
        $html = $this->render('restaurant/restaurant_pdf.html.twig', [
            'restaurants' => $restaurants
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("restaurantList.pdf", [
            "Attachment" => true
        ]);
    }
    /**
     * @Route("/index/places", name="restaurant_places", methods={"GET"})
     */
    public function  orderedByPlace(Request $request, PaginatorInterface $paginator, RestaurantRepository $restaurantRepository): Response
    {
        $restaurant = $restaurantRepository->orderPlace();

        $restaurants = $paginator->paginate($restaurant,$request->query->getInt('page', 1),5);

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }
    /**
     * @Route("/index/name", name="restaurant_name", methods={"GET"})
     */
    public function  orderedByName(Request $request, PaginatorInterface $paginator, RestaurantRepository $restaurantRepository): Response
    {
        $restaurant = $restaurantRepository->orderName();

        $restaurants = $paginator->paginate($restaurant,$request->query->getInt('page', 1),5);

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }
    /**
     * @Route("/index/specialite", name="restaurant_specialite", methods={"GET"})
     */
    public function  orderedBySpecialite(Request $request, PaginatorInterface $paginator, RestaurantRepository $restaurantRepository): Response
    {
        $restaurant = $restaurantRepository->orderSpecialite();

        $restaurants = $paginator->paginate($restaurant,$request->query->getInt('page', 1),5);

        return $this->render('restaurant/index.html.twig', [
            'restaurants' => $restaurants,
        ]);
    }
}
