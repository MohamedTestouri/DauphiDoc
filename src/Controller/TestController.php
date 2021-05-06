<?php

namespace App\Controller;

use App\Entity\Test;
use App\Form\TestType;
use App\Repository\TestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;

/**
 * @Route("/test")
 */
class TestController extends AbstractController
{
    /**
     * @Route("/", name="test_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $test = $this->getDoctrine()
            ->getRepository(Test::class)
            ->findAll();

        $tests = $paginator->paginate($test,$request->query->getInt('page', 1),5);

        return $this->render('test/index.html.twig', [
            'tests' => $tests,
        ]);
    }
    /**
     * @Route("/front", name="test_index_front", methods={"GET"})
     */
    public function indexFront(): Response
    {
        $test = $this->getDoctrine()
            ->getRepository(Test::class)
            ->findAll();
        return $this->render('test/front_index.html.twig', [
            'tests' => $test,
        ]);
    }

    /**
     * @Route("/new", name="test_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $test = new Test();
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($test);
            $entityManager->flush();

            return $this->redirectToRoute('test_index');
        }

        return $this->render('test/new.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idtest}", name="test_show", methods={"GET"})
     */
    public function show(Test $test): Response
    {
        return $this->render('test/show.html.twig', [
            'test' => $test,
        ]);
    }

    /**
     * @Route("/{idtest}/edit", name="test_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Test $test): Response
    {
        $form = $this->createForm(TestType::class, $test);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('test_index');
        }

        return $this->render('test/edit.html.twig', [
            'test' => $test,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idtest}", name="test_delete", methods={"POST"})
     */
    public function delete(Request $request, Test $test): Response
    {
        if ($this->isCsrfTokenValid('delete'.$test->getIdtest(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($test);
            $entityManager->flush();
        }

        return $this->redirectToRoute('test_index');
    }

    /**
     * @Route("/index/category", name="test_category", methods={"GET"})
     */
    public function  orderedByCategory(Request $request, PaginatorInterface $paginator, TestRepository $testRepository): Response
{
    $test = $testRepository->categoryOrder();

    $tests = $paginator->paginate($test,$request->query->getInt('page', 1),5);

    return $this->render('test/index.html.twig', [
        'tests' => $tests,
    ]);
}

    /**
     * @Route("/index/teacher", name="test_teacher", methods={"GET"})
     */
    public function  orderedByTeacher(Request $request, PaginatorInterface $paginator, TestRepository $testRepository): Response
    {
        $test = $testRepository->noenseigOrder();

        $tests = $paginator->paginate($test,$request->query->getInt('page', 1),5);

        return $this->render('test/index.html.twig', [
            'tests' => $tests,
        ]);
    }

    /**
     * @Route("/index/pdf", name="test_pdf", methods={"GET"})
     */
    public function pdfGenerator():Response{
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
       $tests=  $this->getDoctrine()
            ->getRepository(Test::class)
            ->findAll();
        $html = $this->render('test/test_pdf.html.twig', [
            'tests' => $tests
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("testList.pdf", [
            "Attachment" => true
        ]);
    }
}
