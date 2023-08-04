<?php

namespace Rahmanramsi\LivewirePageGroup\Commands;

use Illuminate\Console\Command;

class LivewirePageGroupCommand extends Command
{
    public $signature = 'livewire-page-group';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
