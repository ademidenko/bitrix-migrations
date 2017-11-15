<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Arrilot\BitrixMigrations\Migrator;
use Symfony\Component\Console\Question\ChoiceQuestion;

class MigrateByNameCommand extends AbstractCommand
{
    /**
     * Migrator instance.
     *
     * @var Migrator
     */
    protected $migrator;

    /**
     * Constructor.
     *
     * @param Migrator $migrator
     */
    public function __construct(Migrator $migrator)
    {
        $this->migrator = $migrator;
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('migrate-one')->setDescription('Run selected migration');
    }

    /**
     * Execute the console command.
     *
     * @return null|int
     */
    protected function fire()
    {
        $toRun = $this->migrator->getMigrationsToRun();

        if (!empty($toRun)) {
            $questionHelper = $this->getHelper('question');
            $question = new ChoiceQuestion('<info>Which migration to migrate?</info>', $toRun, false);
            $migrationName = $questionHelper->ask($this->input, $this->output, $question);

            if (!$toRun[$migrationName]) {
                throw new MigrationException('Invalid migration name '.$migrationName);
            } else {
                $this->migrator->runMigration($migrationName);
            }
        } else {
            $this->info('Nothing to migrate');
        }
    }

}
