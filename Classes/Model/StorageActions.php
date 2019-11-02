<?php
declare(strict_types = 1);

namespace LMS3\Support\Model;

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

use \TYPO3\CMS\Extbase\Persistence\Repository;
use TYPO3\CMS\Core\Utility\ClassNamingUtility;

/**
 * @author Sergey Borulko <borulkosergey@icloud.com>
 */
trait StorageActions
{
    /**
     * @return \TYPO3\CMS\Extbase\Persistence\Repository
     */
    public static function repository(): Repository
    {
        $repository = ClassNamingUtility::translateModelNameToRepositoryName(get_called_class());

        return $repository::make();
    }

    /**
     * @param array $properties
     *
     * @return \LMS3\Support\Model\AbstractModel
     */
    public static function create(array $properties = []): AbstractModel
    {
        $entity = self::repository()->produce($properties);
        $entity->save();

        return $entity;
    }

    /**
     * Persists the new entity or updates it
     */
    public function save(): void
    {
        $this->_isNew() ?
            self::repository()->persist($this) : self::repository()->upgrade($this);
    }

    /**
     *  Remove entity from database
     *
     * @return bool
     */
    public function delete(): bool
    {
        return self::repository()->destroy($this);
    }
}