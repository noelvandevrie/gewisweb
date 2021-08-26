<?php

namespace Company\Mapper;

use Application\Mapper\BaseMapper;
use Company\Model\Job as JobModel;

/**
 * Mappers for jobs.
 *
 * NOTE: Jobs will be modified externally by a script. Modifications will be
 * overwritten.
 */
class Job extends BaseMapper
{
    /**
     * Checks if $slugName is only used by object identified with $cid.
     *
     * @param string $companySlug
     * @param string $slugName The slugName to be checked
     * @param int $jid
     * @param int $category
     * @return bool
     */
    public function isSlugNameUnique($companySlug, $slugName, $jid, $category)
    {
        // A slug in unique if there is no other slug of the same category and same language
        $objects = $this->findJob(
            [
                'companySlugName' => $companySlug,
                'jobSlug' => $slugName,
                'jobCategoryId' => $category,
            ]
        );
        foreach ($objects as $job) {
            // If the current job is in the database under the same slug, we can safely skip it
            if ($job->getId() == $jid) {
                continue;
            }

            return false;
        }

        return true;
    }

    /**
     * Inserts a job into a given package.
     *
     * @param mixed $package
     */
    public function insertIntoPackage($package, $lang, $languageNeutralId)
    {
        $job = new JobModel();
        $job->setLanguage($lang);
        $job->setLanguageNeutralId($languageNeutralId);
        $job->setPackage($package);

        return $job;
    }

    public function findJobsWithoutCategory($lang)
    {
        $qb = $this->getRepository()->createQueryBuilder('j');
        $qb->select('j');
        $qb->where('j.category is NULL');
        $qb->andWhere('j.language=:lang');
        $qb->setParameter('lang', $lang);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find all jobs identified by $jobSlugName that are owned by a company
     * identified with $companySlugName.
     *
     * @param array $dict
     * @return int|mixed|string
     */
    public function findJob($dict)
    {
        $qb = $this->getRepository()->createQueryBuilder('j');
        $qb->select('j')->join('j.package', 'p')->join('p.company', 'c');
        if (array_key_exists('jobCategory', $dict) || array_key_exists('jobCategoryId', $dict)) {
            $qb->join('j.category', 'cat');
        }
        if (array_key_exists('jobSlug', $dict)) {
            $jobSlugName = $dict['jobSlug'];
            $qb->andWhere('j.slugName=:jobId');
            $qb->setParameter('jobId', $jobSlugName);
        }
        if (array_key_exists('languageNeutralId', $dict)) {
            $languageNeutralId = $dict['languageNeutralId'];
            $qb->andWhere('j.languageNeutralId=?1 OR j.id=?2');
            $qb->setParameter(1, $languageNeutralId);
            $qb->setParameter(2, $languageNeutralId);
        }

        if (array_key_exists('jobCategory', $dict)) {
            $category = $dict['jobCategory'];
            $qb->andWhere('cat.slug=:category');
            $qb->setParameter('category', $category);
        }
        if (array_key_exists('jobCategoryId', $dict)) {
            $category = $dict['jobCategoryId'];
            $qb->andWhere('cat.id=:category');
            $qb->setParameter('category', $category);
        }

        if (array_key_exists('language', $dict)) {
            $lang = $dict['language'];
            $qb->andWhere('j.language=:language');
            $qb->setParameter('language', $lang);
        }

        if (array_key_exists('companySlugName', $dict)) {
            $companySlugName = $dict['companySlugName'];
            $qb->andWhere('c.slugName=:companySlugName');
            $qb->setParameter('companySlugName', $companySlugName);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Deletes the jobs corresponding to the given language neutral id.
     */
    public function deleteByLanguageNeutralId($jobId)
    {
        $jobs = $this->getRepository()->findBy(['languageNeutralId' => $jobId]);
        foreach ($jobs as $job) {
            $this->em->remove($job);
        }

        $this->em->flush();
    }

    public function createObjectSelectConfig($targetClass, $property, $label, $name, $locale)
    {
        return [
            'name' => $name,
            'type' => 'DoctrineModule\Form\Element\ObjectSelect',
            'options' => [
                'label' => $label,
                'object_manager' => $this->em,
                'target_class' => $targetClass,
                'property' => $property,
                'find_method' => [
                    'name' => 'findBy',
                    'params' => [
                        'criteria' => ['language' => $locale],
                        // Use key 'orderBy' if using ORM
                        //'orderBy'  => ['lastname' => 'ASC'],
                    ],
                ],
            ],
            //'attributes' => [
            //'class' => 'form-control input-sm'
            //]
        ];
    }

    /**
     * @inheritDoc
     */
    protected function getRepositoryName(): string
    {
        return JobModel::class;
    }
}
