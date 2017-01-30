<?php

namespace VPNAdmin\ApiBundle\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    const DEFAULT_SORTBY = 'name';
    const DEFAULT_SORTDIR = 'ASC';

    protected static $sortBy = array('name', 'email');

    public function findAll($sortBy = null, $sortDir = null)
    {
        $orderBy = array(
            $this->sortBy($sortBy) => $this->sortDir($sortDir)
        );
        return $this->findBy(array(), $orderBy);
    }

    protected function sortBy($field)
    {
        $field = is_string($field) ? strtolower($field) : self::DEFAULT_SORTBY;
        if (in_array($field, self::$sortBy)) {
            return $field;
        }
        return self::DEFAULT_SORTBY;
    }

    protected function sortDir($dir)
    {
        $dir = is_string($dir) ? strtoupper($dir) : self::DEFAULT_SORTDIR;
        if ($dir === 'ASC' || $dir === 'DESC') {
            return $dir;
        }
        return self::DEFAULT_SORTDIR;
    }
}
