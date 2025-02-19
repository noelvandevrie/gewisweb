<?php

declare(strict_types=1);

namespace Education\Mapper;

use Application\Mapper\BaseMapper;
use Education\Model\Course as CourseModel;
use Education\Model\CourseDocument as CourseDocumentModel;
use Education\Model\Exam as ExamModel;
use Education\Model\Summary as SummaryModel;

/**
 * Mapper for course documents.
 *
 * @template-extends BaseMapper<CourseDocumentModel>
 */
class CourseDocument extends BaseMapper
{
    /**
     * @psalm-param class-string<ExamModel>|class-string<SummaryModel> $type
     *
     * @return CourseDocumentModel[]
     */
    public function findDocumentsByCourse(
        CourseModel $course,
        string $type,
    ): array {
        $qb = $this->getRepository()->createQueryBuilder('d');
        $qb->where('d.course = :course')
            ->andWhere('d INSTANCE OF :type')
            ->setParameter('course', $course)
            ->setParameter('type', $this->getEntityManager()->getClassMetadata($type));

        return $qb->getQuery()->getResult();
    }

    protected function getRepositoryName(): string
    {
        return CourseDocumentModel::class;
    }
}
