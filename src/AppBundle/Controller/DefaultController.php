<?php

namespace AppBundle\Controller;

use AppBundle\Annotation\CheckRequest;
use AppBundle\Entity\Note;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Validator\Constraints\File;
use AppBundle\Form\NoteType;


/**
 * Class DefaultController
 * @package AppBundle\Controller
 * @Route("/", name="notes_manage")
 */
class DefaultController extends Controller
{

//    Creating a new linktype or notetype note

    /**
     * @Route("/createNew", name="create_new_note")
     * @Method("POST")
     * @CheckRequest(name = "data")
     */
    public function createAction(array $data=null)
    {
        $note = new Note();
        $form = $this->createForm(NoteType::class, $note);
        $form->submit($data);
        if (!$form->isValid()) {
            return new JsonResponse(array('uspesno' => false));
        }
        $formData = $form->getData();
        $note->setType($data['type']);
        if ($formData->getTitle()) {
            $note->setTitle($formData->getTitle());
            $note->setContent($formData->getContent());
        } else {
            $note->setContent($formData->getContent());
        }
        $em = $this->getDoctrine()->getManager();

        $em->persist($note);
        $em->flush();
        return new JsonResponse(array('uspesno' => true));
    }

//    Creating a new imagetype note

    /**
     * @Route("/createNewImage")
     * @Method("POST")
     */
    public function createImageAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');
        $note = new Note();
        $formBuilder = $this->createFormBuilder();
        $formBuilder->add('file', FileType::class, [
            'required'    => true,
            'constraints' => [
                new File([
                    'maxSize'          => '3M',
                    'mimeTypes'        => [
                        'image/jpg', 'image/jpeg', 'image/png',
                    ],
                ]),
            ],
        ]);
        $form = $formBuilder->getForm();
        $form->submit($request->files->all());
        $data = $form->getData();
        $file = $data['file'];
        if(!$form->isValid()) {
            return new JsonResponse(array('uspesno' => false));
        }
        if(!($file instanceof UploadedFile)) {
            return new JsonResponse(array('uspesno' => false));
        }
        $uploadName = md5(uniqid()) . '.' . $file->guessExtension();
        $file->move($this->getParameter('note_image_dir'), $uploadName);
        $url = $this->get('assets.packages')
            ->getUrl('uploads/images/' . $uploadName);
        $note->setType('image');
        $note->setContent($url);

        $em->persist($note);
        $em->flush($note);
        return new JsonResponse(array('uspesno' => true));
    }

//    Editing a note

    /**
     * @Route("/{id}/edit")
     * @Method("POST")
     * @ParamConverter("note")
     * @CheckRequest(name = "data")
     */
    public function editNoteAction(array $data=null, Note $note){
        $form = $this->createForm(NoteType::class, $note);
        $form->submit($data);
        if (!$form->isValid()) {
            return new JsonResponse(array('uspesno' => false));
        }
        $formData = $form->getData();
        $note->setType($data['type']);
        if ($formData->getTitle()) {
            $note->setTitle($formData->getTitle());
            $note->setContent($formData->getContent());
        } else {
            $note->setContent($formData->getContent());
        }
        $em = $this->getDoctrine()->getManager();

        $em->persist($note);
        $em->flush();
        return new JsonResponse(array('uspesno' => true));
    }

//    Getting all notes

    /**
     * @Route("/getAll", name="get_all_notes")
     * @Method("GET")
     */
    public function getAllNotesAction()
    {
        $repository = $this->getDoctrine()->getRepository(Note::class);
        $notes = $repository->findAll();

        return new JsonResponse($notes);
    }


//    Deleting all notes from trashcan
    /**
     * @Route("/deleteAll", name="delete_all_notes")
     * @Method("DELETE")
     */

    public function deleteAllAction(Request $request){
        $em = $this->getDoctrine()->getManager();
        $fortrash = $em->getRepository('AppBundle:Note')
            ->getTrashed();
        array_walk($fortrash, array($this, 'deleteEntity'), $em);

        $em->flush();

        return new JsonResponse(array('uspesno' => true));
    }

    protected function deleteEntity($entity, $key, $em){
        $em->remove($entity);
    }


//    Deleting a single note from trashcan
    /**
     * @Route("/{id}/delete", requirements={"id": "\d+"}, name="delete_note")
     * @Method("DELETE")
     * @ParamConverter("note")
     */

    public function deleteAction(Request $request, Note $note){
        $em = $this->getDoctrine()->getManager();

        $em->remove($note);
        $em->flush();

        return new JsonResponse(array('uspesno' => true));
    }

//    Editing color of a note

    /**
     * @Route(
     *     "/{id}/edit/{color}",
     *      requirements={"id": "\d+"},
     *      name="edit_color")
     * @Method("POST")
     * @ParamConverter("note")
     */

    public function editColorAction(Request $request, Note $note, $color){
        $em = $this->getDoctrine()->getManager();
        $note->setColor(urldecode($color));

        $em->persist($note);
        $em->flush();

        return new JsonResponse(array('uspesno' => true));
    }

//    Putting note to trashcan
    /**
     * @Route(
     *     "/{id}/remove",
     *     requirements={"id": "\d+"},
     *     name="for_removal")
     * @Method("POST")
     * @ParamConverter("note")
     */

    public function removeAction(Request $request, Note $note){
        $em = $this->getDoctrine()->getManager();
        $note->setTrashed(!$note->getTrashed());

        $em->persist($note);
        $em->flush();

        return new JsonResponse(array('uspesno' => true));
    }

}

