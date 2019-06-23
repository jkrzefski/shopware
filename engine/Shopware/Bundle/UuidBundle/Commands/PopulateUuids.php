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

namespace Shopware\Bundle\UuidBundle\Commands;

use RuntimeException;
use Shopware\Commands\ShopwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class PopulateUuids extends ShopwareCommand
{
    protected function configure()
    {
        $this
            ->addArgument('table', InputArgument::OPTIONAL, 'Select a table to populate with UUIDs.')
            ->addOption('all', 'a', InputOption::VALUE_NONE, 'Populate every possible table with UUIDs.')
            ->addUsage('To be compatible with UUIDs, a table need the columns `id` and `uuid`.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$input->getArgument('table') && !$input->getOption('all')) {
            throw new RuntimeException('You have to either specify one table or include the --all option.');
        }

        $populator = $this->container->get('shopware_bundle_uuid.service_core.uuid_populator');

        if ($input->getArgument('table')) {
            $populator->populate($input->getArgument('table'));
        } elseif ($input->getOption('all')) {
            $populator->populateAll();
        }
    }
}
