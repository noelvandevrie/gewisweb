<?php

namespace Activity\Mapper;

use Activity\Model\ActivityCategory as ActivityCategoryModel;
use Application\Mapper\BaseMapper;

/**
 * @template-extends BaseMapper<ActivityCategoryModel>
 */
class ActivityCategory extends BaseMapper
{
    /**
     * @inheritDoc
     */
    protected function getRepositoryName(): string
    {
        return ActivityCategoryModel::class;
    }
}
