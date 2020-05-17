<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ManagerInterface;
use App\RelationshipVoter\AbstractVoter;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class BaseController
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Handles GET request for a single entity
     */
    public function getOne(
        string $version,
        string $object,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $dto = $this->manager->findDTOBy(['id' => $id]);

        if (! $dto) {
            throw new NotFoundHttpException(sprintf("%s/%s was not found.", $object, $id));
        }

        $values = $authorizationChecker->isGranted(AbstractVoter::VIEW, $dto) ? [$dto] : [];

        return $builder->buildPluralResponse($object, $values, Response::HTTP_OK);
    }

    /**
     * Handles GET request for multiple entities
     */
    public function getAll(
        string $version,
        string $object,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $parameters = ApiRequestParser::extractParameters($request);
        $dtos = $this->manager->findDTOsBy(
            $parameters['criteria'],
            $parameters['orderBy'],
            $parameters['limit'],
            $parameters['offset']
        );

        $filteredResults = array_filter($dtos, function ($object) use ($authorizationChecker) {
            return $authorizationChecker->isGranted(AbstractVoter::VIEW, $object);
        });

        //Re-index numerically index the array
        $values = array_values($filteredResults);

        return $builder->buildPluralResponse($object, $values, Response::HTTP_OK);
    }

    /**
     * Handles POST which creates new data in the API
     */
    public function post(
        string $version,
        string $object,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ) {
        $class = $this->manager->getClass() . '[]';

        $entities = $requestParser->extractEntitiesFromPostRequest($request, $class, $object);

        foreach ($entities as $entity) {
            $errors = $validator->validate($entity);
            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
            }
            if (! $authorizationChecker->isGranted(AbstractVoter::CREATE, $entity)) {
                throw new AccessDeniedException('Unauthorized access!');
            }
        }

        foreach ($entities as $entity) {
            $this->manager->update($entity, false);
        }
        $this->manager->flush();

        return $builder->buildPluralResponse($object, $entities, Response::HTTP_CREATED);
    }

    /**
     * Modifies a single object in the API.  Can also create and
     * object if it does not yet exist.
     */
    public function put(
        string $version,
        string $object,
        string $id,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ) {
        $entity = $this->manager->findOneBy(['id' => $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = AbstractVoter::EDIT;
        } else {
            $entity = $this->manager->create();
            $code = Response::HTTP_CREATED;
            $permission = AbstractVoter::CREATE;
        }

        $entity = $requestParser->extractEntityFromPutRequest($request, $entity, $object);

        $errors = $validator->validate($entity);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
        }
        if (! $authorizationChecker->isGranted($permission, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $this->manager->update($entity, true, false);

        return $builder->buildSingularResponse($object, $entity, $code);
    }
}
