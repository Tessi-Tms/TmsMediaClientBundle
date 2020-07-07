<?php

namespace  Tms\Bundle\MediaClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Tms\Bundle\MediaClientBundle\Form\Type\TmsAjaxMediaUploadType;
use Tms\Bundle\MediaClientBundle\Model\Media;

/**
 * @Route("{_locale}/upload")
 */
class UploadController extends Controller
{
    /**
     * @Route("/{field}", name="tms_media_client_ajax_upload")
     * @Method("POST")
     *
     * @param Request $request Instance of Request
     * @param string  $field   The field identifier
     *
     * @return JsonResponse
     */
    public function uploadAction(Request $request, $field)
    {
        $provider = $this->get('tms_media_client.storage_provider.tms_media');
        $session = $this->get('session');
        $uniqId = uniqid(sprintf('%s_', $field));

        // Retrieve the formType data
        $data = $session->get(TmsAjaxMediaUploadType::sessionName);

        // Retrieve all the field options
        if (!isset($data[$field]['options'])) {
            return new JsonResponse(array(
                'message' => 'Field configuration not found'
            ), JsonResponse::HTTP_NOT_FOUND);
        }

        // Remove the previous uploads
        if (isset($data[$field]['uploads']) && is_array($data[$field]['uploads'])) {
            foreach ($data[$field]['uploads'] as $upload) {
                if (isset($upload['providerReference'])) {
                    $provider->remove($upload['providerReference']);
                }
            }
        }
        $data[$field]['uploads'] = array();
        $session->set(TmsAjaxMediaUploadType::sessionName, $data);

        // Generate the upload form
        $form = $this->createForm(TmsAjaxMediaUploadType::class, new Media(), array_merge(
            $data[$field]['options'],
            array(
                'csrf_protection' => false,
            )
        ));

        // Handle the post data
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                // Save the media response
                $data[$field]['uploads'] = array(
                    $uniqId => $form->getData()->getPublicData(),
                );
                $session->set(TmsAjaxMediaUploadType::sessionName, $data);

                return new JsonResponse(array(
                    'id' => $uniqId,
                ), JsonResponse::HTTP_OK);
            }

            // Return the first error message
            foreach ($form->getErrors() as $error) {
                return new JsonResponse(array(
                    'message' => $error->getMessage()
                ), JsonResponse::HTTP_EXPECTATION_FAILED);
            }
        }

        // No file uploaded ?
        return new JsonResponse(array(
            'message' => 'File not uploaded'
        ), JsonResponse::HTTP_NOT_FOUND);
    }
}
