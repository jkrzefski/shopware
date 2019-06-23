<?php declare(strict_types=1);
/**
 * Shopware 5
 * Copyright (c) shopware AG
 *
 * According to our dual licensing model, this program can be used either
 * under the terms of the GNU Affero General Public License, version 3,
 * or under a proprietary license.
 *
 * The texts of the GNU Affero General Public License with an additional
 * permission and of our proprietary license can be found at and
 * in the LICENSE file you have received along with this program.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * "Shopware" is a registered trademark of shopware AG.
 * The licensing of the program under the AGPLv3 does not imply a
 * trademark license. Therefore any rights, title and interest in
 * our trademarks remain entirely with us.
 */

namespace Shopware\Bundle\UuidBundle\Service\Core;

use Doctrine\DBAL\Connection;
use PDO;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Shopware\Bundle\UuidBundle\Service\UuidPopulatorInterface;

class UuidPopulator implements UuidPopulatorInterface
{
    /**
     * @var Connection
     */
    private $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function populate(string $table, bool $throwExceptions = true): void
    {
        $columns = $this->connection->fetchAll(sprintf('show columns from %s where `Field` in ("id", "uuid");', $table));
        $columns = array_column($columns, 'Field');

        if (!in_array('id', $columns)) {
            if ($throwExceptions) {
                throw new RuntimeException(sprintf('Cannot populate table %s with uuids, because the `id` column is missing.', $table));
            }

            return;
        }

        if (!in_array('uuid', $columns)) {
            if ($throwExceptions) {
                throw new RuntimeException(sprintf('Cannot populate table %s with uuids, because the `uuid` column is missing.', $table));
            }

            return;
        }

        $builder = $this->connection->createQueryBuilder();

        $selectStatement = $builder->select('id')->from($table)->where($builder->expr()->isNull('uuid'))->execute();
        $updateStatement = $builder->update($table)->set('uuid', ':uuid')->where($builder->expr()->eq('id', ':id'));

        while ($id = $selectStatement->fetch(PDO::FETCH_COLUMN)) {
            $updateStatement->setParameter('id', $id);
            $updateStatement->setParameter('uuid', Uuid::uuid4());

            $updateStatement->execute();
        }
    }

    public function populateAll(): void
    {
        $tables = $this->connection->executeQuery('show tables;')->fetchAll(PDO::FETCH_COLUMN);

        foreach ($tables as $table) {
            $this->populate($table, false);
        }
    }
}
