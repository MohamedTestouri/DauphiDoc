<?php

namespace App\Controller;

use App\Entity\Note;
use App\Entity\Test;
use App\Form\FrontNoteType;
use App\Form\NoteType;
use App\Repository\NoteRepository;
use App\Repository\TestRepository;
use Dompdf\Dompdf;
use Dompdf\Options;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/note")
 */
class NoteController extends AbstractController
{
    /**
     * @Route("/{id}/answer", name="note_answer", methods={"GET","POST"})
     */
    public function answer($id, Request $request):Response{
        $test = $this->getDoctrine()
            ->getRepository(Test::class)
            ->find($id);
        $note = new Note();
        $form = $this->createForm(FrontNoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
        $note->setNote(1);
        $note->setNomtest($test->getNom());
            $entityManager->persist($note);
            $entityManager->flush();
           return $this->redirectToRoute('test_index_front');
        }
        return $this->render('note/answer.html.twig', [
            'test'=>$test,
            'note'=>$note,
            'form' => $form->createView(),
            ]);
    }

    /**
     * @Route("/", name="note_index", methods={"GET"})
     */
    public function index(Request $request, PaginatorInterface $paginator, NoteRepository $noteRepository): Response
    {
        $note = $this->getDoctrine()
            ->getRepository(Note::class)
            ->findAll();
        $notes = $paginator->paginate($note,$request->query->getInt('page', 1),5);
        return $this->render('note/index.html.twig', [
            'notes' => $notes,
            'admis' => $noteRepository->statsAdmis(),
            'fails' => $noteRepository->statsFails(),

        ]);
    }

    /**
     * @Route("/new", name="note_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {

        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($note);
            $entityManager->flush();

            return $this->redirectToRoute('note_index');
        }

        return $this->render('note/new.html.twig', [
            'note' => $note,
                        'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idnote}", name="note_show", methods={"GET"})
     */
    public function show(Note $note): Response
    {

        return $this->render('note/show.html.twig', [
            'note' => $note,
        ]);
    }

    /**
     * @Route("/{idnote}/edit", name="note_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Note $note): Response
    {
        $form = $this->createForm(NoteType::class, $note);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('note_index');
        }

        return $this->render('note/edit.html.twig', [
            'note' => $note,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{idnote}", name="note_delete", methods={"POST"})
     */
    public function delete(Request $request, Note $note): Response
    {
        if ($this->isCsrfTokenValid('delete'.$note->getIdnote(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($note);
            $entityManager->flush();
        }

        return $this->redirectToRoute('note_index');
    }
    /**
     * @Route("/index/pdf", name="note_pdf", methods={"GET"})
     */
    public function PDFGenerator():Response{
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($pdfOptions);
        $notes=  $this->getDoctrine()
            ->getRepository(Note::class)
            ->findAll();
        $html = $this->render('note/note_pdf.html.twig', [
            'notes' => $notes
        ]);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("NotesList.pdf", [
            "Attachment" => true
        ]);
    }

    /**
     * @Route("/index/note", name="note_note", methods={"GET"})
     */
    public function  orderedByNote(Request $request, PaginatorInterface $paginator, NoteRepository $noteRepository): Response
    {
        $note = $noteRepository->noteOrder();

        $notes = $paginator->paginate($note,$request->query->getInt('page', 1),5);

        return $this->render('note/index.html.twig', [
            'notes' => $notes,
            'admis' => $noteRepository->statsAdmis(),
            'fails' => $noteRepository->statsFails(),
        ]);
    }
    /**
     * @Route("/index/name", name="note_name", methods={"GET"})
     */
   public function  orderedByName(Request $request, PaginatorInterface $paginator, NoteRepository $noteRepository): Response
    {
        $note = $noteRepository->studentNameOrder();

        $notes = $paginator->paginate($note,$request->query->getInt('page', 1),5);

        return $this->render('note/index.html.twig', [
            'notes' => $notes,
            'admis' => $noteRepository->statsAdmis(),
            'fails' => $noteRepository->statsFails(),
        ]);
    }
}
