<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Entity\UserMadeReminderInterface;

/**
 * Class UserMadeReminderController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("UserMadeReminders")
 */
class UserMadeReminderController extends FOSRestController
{
    /**
     * Get a UserMadeReminder
     *
     * @ApiDoc(
     *   section = "UserMadeReminder",
     *   description = "Get a UserMadeReminder.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="UserMadeReminder identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   statusCodes={
     *     200 = "UserMadeReminder.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {
        $userMadeReminder = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $userMadeReminder)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $answer['userMadeReminders'][] = $userMadeReminder;

        return $answer;
    }

    /**
     * Get all UserMadeReminder.
     *
     * @ApiDoc(
     *   section = "UserMadeReminder",
     *   description = "Get all UserMadeReminder.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   statusCodes = {
     *     200 = "List of all UserMadeReminder",
     *     204 = "No content. Nothing to list."
     *   }
     * )
     *
     * @QueryParam(
     *   name="offset",
     *   requirements="\d+",
     *   nullable=true,
     *   description="Offset from which to start listing notes."
     * )
     * @QueryParam(
     *   name="limit",
     *   requirements="\d+",
     *   default="20",
     *   description="How many notes to return."
     * )
     * @QueryParam(
     *   name="order_by",
     *   nullable=true,
     *   array=true,
     *   description="Order by fields. Must be an array ie. &order_by[name]=ASC&order_by[description]=DESC"
     * )
     * @QueryParam(
     *   name="filters",
     *   nullable=true,
     *   array=true,
     *   description="Filter by fields. Must be an array ie. &filters[id]=3"
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $criteria);
        if (array_key_exists('createdAt', $criteria)) {
            $criteria['createdAt'] = new \DateTime($criteria['createdAt']);
        }
        if (array_key_exists('dueDate', $criteria)) {
            $criteria['dueDate'] = new \DateTime($criteria['dueDate']);
        }

        $manager = $this->container->get('ilioscore.usermadereminder.manager');
        $result = $manager->findBy($criteria, $orderBy, $limit, $offset);

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        //If there are no matches return an empty array
        $answer['userMadeReminders'] = $result ? array_values($result) : [];

        return $answer;
    }

    /**
     * Create a UserMadeReminder.
     *
     * @ApiDoc(
     *   section = "UserMadeReminder",
     *   description = "Create a UserMadeReminder.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\UserMadeReminderType",
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   statusCodes={
     *     201 = "Created UserMadeReminder.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $handler = $this->container->get('ilioscore.usermadereminder.handler');
            $userMadeReminder = $handler->post($this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $userMadeReminder)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager = $this->container->get('ilioscore.usermadereminder.manager');
            $manager->update($userMadeReminder, true, false);

            $answer['userMadeReminders'] = [$userMadeReminder];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a UserMadeReminder.
     *
     * @ApiDoc(
     *   section = "UserMadeReminder",
     *   description = "Update a UserMadeReminder entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\UserMadeReminderType",
     *   output="Ilios\CoreBundle\Entity\UserMadeReminder",
     *   statusCodes={
     *     200 = "Updated UserMadeReminder.",
     *     201 = "Created UserMadeReminder.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function putAction(Request $request, $id)
    {
        try {
            $manager = $this->container->get('ilioscore.usermadereminder.manager');
            $userMadeReminder = $manager->findOneBy(['id'=> $id]);
            if ($userMadeReminder) {
                $code = Codes::HTTP_OK;
            } else {
                $userMadeReminder = $manager->create();
                $code = Codes::HTTP_CREATED;
            }

            $handler = $this->container->get('ilioscore.usermadereminder.handler');
            $userMadeReminder = $handler->put($userMadeReminder, $this->getPostData($request));

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $userMadeReminder)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($userMadeReminder, true, true);

            $answer['userMadeReminder'] = $userMadeReminder;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a UserMadeReminder.
     *
     * @ApiDoc(
     *   section = "UserMadeReminder",
     *   description = "Delete a UserMadeReminder entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "UserMadeReminder identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted UserMadeReminder.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal UserMadeReminderInterface $userMadeReminder
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $userMadeReminder = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $userMadeReminder)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.usermadereminder.manager');
            $manager->delete($userMadeReminder);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return UserMadeReminderInterface $userMadeReminder
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.usermadereminder.manager');
        $userMadeReminder = $manager->findOneBy(['id' => $id]);
        if (!$userMadeReminder) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $userMadeReminder;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('userMadeReminder')) {
            return $request->request->get('userMadeReminder');
        }

        return $request->request->all();
    }
}
