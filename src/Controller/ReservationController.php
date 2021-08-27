<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Reservation;
use App\Entity\Restaurant;
use App\Entity\Test;
use App\Form\ReservationType;
use App\Repository\ReservationRepository;
use App\Repository\RestaurantRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/reservation")
 */
class ReservationController extends AbstractController
{
    /**
     * @Route("/api/reserver", name="api_reservation_reserver")
     */
    public function reserverApi(Request $request){
        $entityManager = $this->getDoctrine()->getManager();
        $reservation = new Reservation();
        $reservation->setIdrestaurant($request->get('idRestaurant'));
        $reservation->setIduser($request->get('idUser'));
        $reservation->setPlaces($request->get('places'));
        $entityManager->persist($reservation);
        $restaurant = $this->getDoctrine()
            ->getRepository(Restaurant::class)
            ->find($request->get('idRestaurant'));
        $restaurant->setPlaces($restaurant->getPlaces() - $reservation->getPlaces());
        $entityManager->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($reservation);
        return new JsonResponse($formatted);

    }
    /**
     * @Route("/api/show", name="api_reservation_show")
     */
    public function showApi(Request $request){
        $reservation = $this->getDoctrine()->getRepository(Reservation::class)->findBy(array('iduser' => $request->get('idUser')));
        $serializer = new Serializer([new DateTimeNormalizer(), new ObjectNormalizer()]);
        $formatted = $serializer->normalize($reservation);
        return new JsonResponse($formatted);
    }
    /**
     * @Route("/", name="reservation_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $reservation = $this->getDoctrine()
            ->getRepository(Reservation::class)
            ->findAll();
        $reservations = $paginator->paginate($reservation,$request->query->getInt('page', 1),5);
        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * @Route("/new", name="reservation_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $reservation = new Reservation();
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $reservation->setIdrestaurant($request->get('id'));
            $entityManager->persist($reservation);


            $restaurant = $this->getDoctrine()
                ->getRepository(Restaurant::class)
                ->find($request->get('id'));
            $restaurant->setPlaces($restaurant->getPlaces() - $reservation->getPlaces());
            $entityManager->flush();
            return $this->redirectToRoute('restaurant_index_front');
        }

        return $this->render('reservation/new.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_show", methods={"GET"})
     */
    public function show(Reservation $reservation): Response
    {
        return $this->render('reservation/show.html.twig', [
            'reservation' => $reservation,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="reservation_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Reservation $reservation): Response
    {
        $form = $this->createForm(ReservationType::class, $reservation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('reservation_index');
        }

        return $this->render('reservation/edit.html.twig', [
            'reservation' => $reservation,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="reservation_delete", methods={"POST"})
     */
    public function delete(Request $request, Reservation $reservation): Response
    {
        if ($this->isCsrfTokenValid('delete'.$reservation->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($reservation);
            $entityManager->flush();
        }

        return $this->redirectToRoute('reservation_index');
    }
    /**
     * @Route("/index/places", name="reservation_places", methods={"GET"})
     */
    public function  orderedByPlace(Request $request, PaginatorInterface $paginator, ReservationRepository $reservationRepository): Response
    {
        $reservation = $reservationRepository->orderPlace();

        $reservations = $paginator->paginate($reservation,$request->query->getInt('page', 1),5);

        return $this->render('reservation/index.html.twig', [
            'reservations' => $reservations,
        ]);
    }
    /**
     * @Route("/index/pdf", name="reservation_pdf", methods={"GET"})
     */
    public function pdfGenerator():Response{
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $reservations=  $this->getDoctrine()
            ->getRepository(Reservation::class)
            ->findAll();
        $html = $this->render('reservation/reservation_pdf.html.twig', [
            'reservations' => $reservations
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("reservationList.pdf", [
            "Attachment" => true
        ]);
    }
}
