<?php

namespace MWSimple\Bundle\AdminCrudBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * @Route("/")
 */
class BackendController extends Controller
{
    /**
     * @Route("/", name="mws_admin_crud_menu")
     * @Method("GET")
     */
    public function indexAction(Request $request) {
        return $this->render('MWSimpleAdminCrudBundle:Backend:index.html.twig', [
            'config' => null,
        ]);
    }
    /**
     * AJAX Enabled Disabled
     * @Route("/mws_ajax_enabled_disabled", name="mws_ajax_enabled_disabled")
     * @Method("POST")
     */
    public function ajaxEnabledDisabledAction(Request $request)
    {
        $repository = $request->request->get('repository');
        $id = $request->request->get('dataid');
        $fieldname = $request->request->get('datafieldname');

        $em = $this->getDoctrine()->getManager();
        $entity = $em->getRepository($repository)->find($id);

        if ($entity) {
            $fieldname_value = null;
            if (method_exists($entity, "is".$fieldname)) {
                $fieldname_value = "is".$fieldname;
            } elseif (method_exists($entity, "get".$fieldname)) {
                $fieldname_value = "get".$fieldname;
            }

            if (!is_null($fieldname_value) && method_exists($entity, "set".$fieldname)) {
                $fieldname_set = "set".$fieldname;

                try {
                    if ($entity->$fieldname_value()) {
                        $entity->$fieldname_set(false);
                    } else {
                        $entity->$fieldname_set(true);
                    }

                    $em->flush();

                    $res = true;
                } catch (Exception $e) {
                    $res = false;
                }
            } else {
                $res = false;
            }
        } else {
            $res = false;
        }

        $response = new JsonResponse();
        $response->setData($res);

        return $response;
    }
}