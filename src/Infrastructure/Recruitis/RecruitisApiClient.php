<?php

declare(strict_types=1);

namespace App\Infrastructure\Recruitis;

use App\Domain\Job\Job;
use App\Domain\Job\ValueObject\JobId;
use App\Domain\Job\ValueObject\JobTitle;
use App\Domain\Job\ValueObject\Salary;
use App\Domain\Job\ValueObject\JobDescription;
use App\Dto\Application\JobApplicationDto;
use App\Dto\Controller\JobListWithMetaDto;
use App\Dto\Controller\JobListMetaDto;
use App\Infrastructure\Cache\CacheInterface;
use App\Infrastructure\Http\HttpClientInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

final class RecruitisApiClient
{
    private const API_BASE_URL = 'https://app.recruitis.io/api2';
    private const CACHE_TTL = 3600; // 1 hour

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly CacheInterface $cache,
        private readonly LoggerInterface $logger,
        #[Autowire(env: 'default::RECRUITIS_API_TOKEN')]
        private ?string $apiToken,
    ) {
        $this->apiToken = \trim($this->apiToken ?? '');
        if ($this->apiToken === '') {
            throw new \InvalidArgumentException('RECRUITIS_API_TOKEN is not set. Please check your .env file.');
        }
    }

    /**  */
    public function getJobs(int $page = 1, int $limit = 10): JobListWithMetaDto
    {
        $cacheKey = "jobs_page_{$page}_limit_{$limit}";

        try {
            $cachedData = $this->cache->get($cacheKey);
            if ($cachedData instanceof JobListWithMetaDto) {
                $this->logger->info('Jobs retrieved from cache', ['page' => $page, 'limit' => $limit]);
                return $cachedData;
            }

            $response = $this->httpClient->get(
                self::API_BASE_URL . '/jobs',
                [
                    'page' => $page,
                    'limit' => $limit,
                ],
                [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ]
            );

            $jobs = $this->transformJobsResponse($response['payload'] ?? []);
            $apiResponseMeta = $response['meta'] ?? [];
            $meta = new JobListMetaDto(
                page: (int)($apiResponseMeta['page'] ?? $page),
                limit: (int)($apiResponseMeta['limit'] ?? $limit),
                total: (int)($apiResponseMeta['entries_total'] ?? count($jobs))
            );
            $dto = new JobListWithMetaDto($jobs, $meta);

            $this->cache->set($cacheKey, $dto, self::CACHE_TTL);
            $this->logger->info('Jobs retrieved from API and cached', ['page' => $page, 'limit' => $limit]);

            return $dto;
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch jobs from API', [
                'page' => $page,
                'limit' => $limit,
                'error' => $e->getMessage()
            ]);
            throw new RecruitisApiException('Failed to fetch jobs: ' . $e->getMessage(), 0, $e);
        }
    }

    public function getJobById(string $jobId): ?Job
    {
        $cacheKey = "job_{$jobId}";

        try {
            $cachedData = $this->cache->get($cacheKey);
            if ($cachedData !== null) {
                $this->logger->info('Job retrieved from cache', ['job_id' => $jobId]);
                return $cachedData;
            }

            $response = $this->httpClient->get(
                self::API_BASE_URL . "/jobs/{$jobId}",
                [],
                [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ]
            );
            $jobRaw = $response['payload'] ?? [];

            $job = $this->transformJobResponse($jobRaw);

            if ($job !== null) {
                $this->cache->set($cacheKey, $job, self::CACHE_TTL);
                $this->logger->info('Job retrieved from API and cached', ['job_id' => $jobId]);
            }

            return $job;
        } catch (\Exception $e) {
            $this->logger->error('Failed to fetch job from API', [
                'job_id' => $jobId,
                'error' => $e->getMessage()
            ]);
            throw new RecruitisApiException('Failed to fetch job: ' . $e->getMessage(), 0, $e);
        }
    }

    public function submitApplication(JobApplicationDto $applicationDto): void
    {
        $applicationData = [
            'job_id' => $applicationDto->jobId,
            'name' => $applicationDto->firstName . ' ' . $applicationDto->lastName,
            'email' => $applicationDto->email,
            'phone' => $applicationDto->phone,
            'cover_letter' => $applicationDto->coverLetter,
            'linkedin' => $applicationDto->resumeUrl,
        ];

        try {
            $response = $this->httpClient->post(
                self::API_BASE_URL . '/answers',
                $applicationData,
                [
                    'Authorization' => 'Bearer ' . $this->apiToken,
                    'Content-Type' => 'application/json',
                ]
            );

            $this->logger->info('Application submitted successfully', [
                'job_id' => $applicationDto->jobId
            ]);
        } catch (HttpExceptionInterface $e) {
            $this->logger->error('Failed to submit application', [
                'job_id' => $applicationDto->jobId,
                'error' => $e->getMessage()
            ]);
            throw new RecruitisApiException(
                'Failed to submit application: ' . $e->getMessage(),
                code: $e->getCode(),
                previous: $e,
            );
        }
    }

    /**
     * @param array<int, array<string, mixed>> $rawJobList
     * @return list<Job>
     */
    private function transformJobsResponse(
        array $rawJobList
    ): array {
        $jobs = [];
        foreach ($rawJobList as $jobData) {
            try {
                $jobs[] = $this->createJobFromArray($jobData);
            } catch (\Exception $e) {
                $this->logger->warning('Failed to transform job data', [
                    'job_data' => $jobData,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return $jobs;
    }

    /**
     * @param array<string, mixed> $rawJob
     */
    private function transformJobResponse(array $rawJob): ?Job
    {
        try {
            return $this->createJobFromArray($rawJob);
        } catch (\Exception $e) {
            $this->logger->warning('Failed to transform job data', [
                'job_data' => $rawJob,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }

    /**
     * @param array<string, mixed> $jobData
     */
    private function createJobFromArray(array $jobData): Job
    {
        $salary = \implode(
            ' - ',
            [
                $jobData['salary']['min'] ?? '',
                $jobData['salary']['max'] ?? '',
                $jobData['salary']['currency'] ?? ''
            ]
        );
        return new Job(
            id: new JobId((string)($jobData['job_id'] ?? '')),
            title: new JobTitle($jobData['title'] ?? ''),
            salary: new Salary($salary),
            description: new JobDescription($jobData['description'] ?? ''),
            createdAt: new \DateTimeImmutable($jobData['date_created'] ?? 'now'),
            updatedAt: new \DateTimeImmutable($jobData['last_update'] ?? 'now')
        );
    }
}
