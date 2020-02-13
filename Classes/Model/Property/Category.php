<?php
declare(strict_types = 1);

namespace LMS\Facade\Model\Property;

/* * *************************************************************
 *
 *  Copyright notice
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 * ************************************************************* */

use LMS\Facade\Assist\Collection;
use LMS\Facade\Extbase\{User\StateContext, QueryBuilder};
use TYPO3\CMS\Core\Context\Exception\AspectNotFoundException;

/**
 * @author Sergey Borulko <borulkosergey@icloud.com>
 */
trait Category
{
    /**
     * @return \LMS\Facade\Assist\Collection
     */
    public function getCategories(): Collection
    {
        return $this->findCategories();
    }

    /**
     * @return \LMS\Facade\Assist\Collection
     */
    private function findCategories(): Collection
    {
        $builder = QueryBuilder::getQueryBuilderFor('sys_category');

        $constraints = [
            $builder->expr()->in('uid', $this->findRelations()->toArray()),
            $builder->expr()->in('sys_language_uid', $this->getFrontendLanguage()),
        ];

        return Collection::make(
            $builder
                ->select(...['uid', 'title', 'parent'])
                ->from('sys_category')
                ->where(...$constraints)
                ->execute()
                ->fetchAll()
        );
    }

    /**
     * @return \LMS\Facade\Assist\Collection
     */
    private function findRelations(): Collection
    {
        $builder = QueryBuilder::getQueryBuilderFor('sys_category_record_mm');
        $constraints = [
            $builder->expr()->eq('uid_foreign', $builder->createNamedParameter($this->getUid(), \PDO::PARAM_INT)),
            $builder->expr()->eq('tablenames', $builder->createNamedParameter($this->getTableName()))
        ];

        return Collection::make(
            $builder
                ->select('uid_local')
                ->from('sys_category_record_mm')
                ->where(...$constraints)
                ->execute()
                ->fetchAll()
        )->flatten();
    }

    /**
     * @return int
     */
    private function getFrontendLanguage(): int
    {
        try {
            return (int)StateContext::getTypo3Context()->getPropertyFromAspect('language', 'id');
        } catch (AspectNotFoundException $e) {
            return 0;
        }
    }

    /**
     * Returns the name of the model table
     *
     * @return string
     */
    abstract public static function getTableName(): string;
}
