<?php

declare(strict_types=1);

namespace App\Controller;

use App\Application\Job\Query\GetJobsQuery;
use App\Application\Job\Query\GetJobByIdQuery;
use App\Application\Job\Service\JobApplicationService;
use App\Application\Job\Service\JobDtoMapper;
use App\Domain\Job\ValueObject\JobId;
use App\Dto\Controller\JobListResponseDto;
use App\Dto\Controller\JobResponseDto;
use App\Infrastructure\Recruitis\RecruitisApiException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Psr\Log\LoggerInterface;
use App\Application\Job\Service\JobQueryService;
use App\Controller\Common\Trait\ErrorResponsesTrait;
use App\Dto\Controller\JobApplicationRequestDto;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;

#[Route('/api/jobs')]
class JobController extends AbstractController
{
    use ErrorResponsesTrait;

    private const MAX_LIMIT = 10;

    public function __construct(
        private readonly JobQueryService $jobQueryService,
        private readonly JobApplicationService $applicationService,
        private readonly JobDtoMapper $jobDtoMapper,
        private readonly SerializerInterface $serializer,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route('', methods: ['GET'])] // Some people call this the "index" action
    public function getJobs(
        #[MapQueryParameter('page')] int $page = 1,
        #[MapQueryParameter('limit')] int $limit = 10
    ): JsonResponse {
        $limit = min($limit, self::MAX_LIMIT);

        try {
            $jobListWithMeta = $this->jobQueryService->getJobs(new GetJobsQuery($page, $limit));
            $responseDto = new JobListResponseDto(
                success: true,
                data: $this->jobDtoMapper->mapDomainJobListToApiJobDtoList($jobListWithMeta->jobs),
                pagination: $jobListWithMeta->meta
            );

            return new JsonResponse(
                data: $this->serializer->serialize($responseDto, 'json'),
                status: Response::HTTP_OK,
                headers: [],
                json: true
            );
        } catch (RecruitisApiException $e) {
            return $this->handleKnownException(error: $e, message: 'Failed to fetch jobs');
        } catch (\Throwable $e) {
            return $this->handleUnexpectedError($e);
        }
    }

    #[Route('/{id}', methods: ['GET'])]
    public function getJob(string $id): JsonResponse
    {
        try {
            $job = $this->jobQueryService->getJobById(new GetJobByIdQuery(new JobId($id)));

            if ($job === null) {
                return $this->handleNotFound('Job not found');
            }

            $jobDto = $this->jobDtoMapper->mapDomainJobToApiJobDto($job);
            $responseDto = new JobResponseDto(success: true, data: $jobDto);

            return new JsonResponse(
                data: $this->serializer->serialize($responseDto, 'json'),
                status: Response::HTTP_OK,
                headers: [],
                json: true
            );
        } catch (RecruitisApiException $e) {
            return $this->handleKnownException(error: $e, message: 'Failed to fetch job', context: ['job_id' => $id]);
        } catch (\Throwable $e) {
            return $this->handleUnexpectedError($e);
        }
    }

    #[Route('/{id}/apply', methods: ['POST'])]
    public function applyForJob(string $id, Request $request): JsonResponse
    {
        try {
            $dto = $this->serializer->deserialize($request->getContent(), JobApplicationRequestDto::class, 'json');

            $this->applicationService->submitApplication(
                new JobId($id),
                $dto
            );

            $responseDto = new JobResponseDto(success: true);

            return new JsonResponse(
                data: $this->serializer->serialize($responseDto, 'json'),
                status: Response::HTTP_OK,
                headers: [],
                json: true
            );
        } catch (\InvalidArgumentException $e) {
            return $this->handleKnownException(error: $e, message: 'Validation error');
        } catch (RecruitisApiException $e) {
            $info = match ($e->getCode()) {
                400 => 'Validation error - invalid form data',
                404 => 'Job not found',
                default => 'Unknown error ('.$e->getCode().')',
            };
            return $this->handleKnownException(
                error: $e,
                message: 'Failed to submit application: '.$info,
                context: ['job_id' => $id]
            );
        } catch (\Throwable $e) {
            return $this->handleUnexpectedError($e);
        }
    }


}
