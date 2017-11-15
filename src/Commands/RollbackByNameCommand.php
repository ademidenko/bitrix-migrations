<?php

namespace Arrilot\BitrixMigrations\Commands;

use Arrilot\BitrixMigrations\Exceptions\MigrationException;
use Arrilot\BitrixMigrations\Migrator;
use Symfony\Component\Console\Question\ChoiceQuestion;

class RollbackByNameCommand extends AbstractCommand
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
        $this->setName('rollback-one')->setDescription('Rollback selected migration');
    }

    /**
     * Execute the console command.
     *
     * @return null|int
     */
    protected function fire()
    {
        $availableForRollback = $this->migrator->getRanMigrations();

        if (!empty($availableForRollback)) {
            $questionHelper = $this->getHelper('question');
            $question = new ChoiceQuestion('<info>Which migration to rollback?</info>', $availableForRollback, false);
            $migrationName = $questionHelper->ask($this->input, $this->output, $question);

            if (!$availableForRollback[$migrationName]) {
                throw new MigrationException('Invalid migration name '.$migrationName);
            } else {
                $this->migrator->runMigration($migrationName);
            }
        } else {
            $this->info('Nothing to rollback');
        }
    }
}
