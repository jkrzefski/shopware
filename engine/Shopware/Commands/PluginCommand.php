<?php
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

namespace Shopware\Commands;

use Shopware\Components\CacheManager;
use Shopware\Components\Plugin\Context\InstallContext;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

abstract class PluginCommand extends ShopwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        parent::configure();

        $this->addOption(
            'clear-cache',
            'c',
            InputOption::VALUE_NONE,
            'Clear any neccessary caches'
        );
    }

    /**
     * @param InputInterface   $input
     * @param OutputInterface  $output
     * @param InstallContext[] ...$contexts
     */
    protected function clearCachesIfRequested(InputInterface $input, OutputInterface $output, ...$contexts)
    {
        if (!empty($input->getOption('clear-cache'))) {
            $this->clearCaches($input, $output, ...$contexts);
        } elseif (!empty($this->getScheduledCaches(...$contexts))) {
            $output->writeln([
                'Consider sw:cache:clear to refresh the installation to keep up to the current changes.',
                'Try the --clear-cache option if you run into this more often.',
            ]);
        }
    }

    /**
     * @param InputInterface   $input
     * @param OutputInterface  $output
     * @param InstallContext[] ...$contexts
     */
    protected function clearCaches(InputInterface $input, OutputInterface $output, ...$contexts)
    {
        /** @var CacheManager $cacheManager */
        $cacheManager = $this->container->get('shopware.cache_manager');
        $io = new SymfonyStyle($input, $output);

        $scheduledCaches = $this->getScheduledCaches(...$contexts);

        $successfulCaches = $cacheManager->clearByTags($scheduledCaches);
        $failedCaches = array_diff($scheduledCaches, $successfulCaches);

        if (!empty($failedCaches)) {
            $io->warning(sprintf('Failed to clear caches: %s.', join(', ', $failedCaches)));
        }
        
        if (!empty($successfulCaches)) {
            $io->success(sprintf('Successfully cleared caches: %s.', join(', ', $successfulCaches)));
        }
    }

    /**
     * @param InstallContext[] ...$contexts
     *
     * @return string[]
     */
    protected function getScheduledCaches(...$contexts)
    {
        $tags = [];

        foreach ($contexts as $context) {
            if (!$context instanceof InstallContext || !array_key_exists('cache', $context->getScheduled())) {
                continue;
            }

            $tags = array_merge($tags, $context->getScheduled()['cache']);
        }

        return array_unique($tags);
    }
}
