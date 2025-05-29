<?php

namespace commands;

use attributes\Description;
use interfaces\Command;

#[Description('Returns the system username')]
class Whoami implements Command
{
    public function execute(): string
    {
        return shell_exec('whoami');
    }
}
