<?php

namespace App\Command;

use App\Queue\Api\NewsCreate;
use App\Service\Api\NewsService;
use jcobhams\NewsApi\NewsApiException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Enum\Api\NewsDomainEnum;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand(
    name: 'news:pull',
    description: 'Pull news from news api service',
)]
class NewsPullCommand extends Command
{
    public function __construct(private NewsService $newsService, private MessageBusInterface $bus)
    {
        parent::__construct();
    }

    /**
     * @throws NewsApiException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $news = $this->newsService->getPostsByDomain([NewsDomainEnum::FORBSE->value]);

        if(!empty($news->articles)) {
            foreach ($news->articles as $new) {
                $this->bus->dispatch(new NewsCreate($new));
            }
        }

        return Command::SUCCESS;
    }
}
