<?php

namespace Company\Mapper;

use Application\Mapper\BaseMapper;
use Company\Model\Proposals\JobUpdate as JobUpdateModel;


/**
 * Mappers for job update proposals.
 *
 * @template-extends BaseMapper<JobUpdateModel>
 */
class JobUpdate extends BaseMapper
{
    /**
     * @inheritDoc
     */
    protected function getRepositoryName(): string
    {
        return JobUpdateModel::class;
    }
}
