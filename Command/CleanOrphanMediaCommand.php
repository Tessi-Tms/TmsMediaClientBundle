<?php

/**
 *
 * @author:  Gabriel BONDAZ <gabriel.bondaz@idci-consulting.fr>
 * @author:  Sekou KOÏTA <sekou.koita@supinfo.com>
 * @license: GPL
 *
 */

namespace Tms\Bundle\MediaClientBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\DoctrineCommandHelper;

class CleanOrphanMediaCommand extends ContainerAwareCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('tms:media:clean-orphan')
            ->setDescription('Clean orphan media')
            ->addOption('force', 'f', InputOption::VALUE_NONE, 'if present, orphan media will be removed')
            ->addOption('em', null, InputOption::VALUE_OPTIONAL, 'The entity manager to use for this command', 'default')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command.

To list all orphan media:

<info>php app/console %command.name%</info>

To clean the orphan media:

<info>php app/console %command.name% --force|-f</info>

EOT
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        DoctrineCommandHelper::setApplicationEntityManager(
            $this->getApplication(),
            $input->getOption('em')
        );
        $entityManager = $this->getContainer()->get('doctrine')->getManager();
        $medias = $entityManager->getRepository('TmsMediaClientBundle:Media')->findAll();

        $providerHandler = $this->getContainer()->get('tms_media_client.storage_provider_handler');
        foreach ($medias as $media) {
            var_dump($media->getProviderReference());
            /*
            $storageProvider = $providerHandler->getStorageProvider($media->getProviderName());
            var_dump($storageProvider->getName(), $storageProvider->getMediaPublicUrl($media->getProviderReference()));
            */
        }

        //die('TODO: Add soft delete on media when remove. Use this command to inform provider to delete the media, then do the job.');
    }
}
