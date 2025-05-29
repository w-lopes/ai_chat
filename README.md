# About it

This project uses [llamafile](https://github.com/Mozilla-Ocho/llamafile) as a base (Thank you [Mozilla](https://www.mozilla.org)!)

This project acts like a middleware to interact with llamafile service. So you can create custom commands that will be used instead of getting the AI results.

Ex: You can create a custom command to check the weather that will call and API instead of getting a response from the AI. You can also create custom commands to interact with databases, etc.

## Requirement

- PHP 8.1 or newer
- llamafile service running

## How to start

#### Model

Download a model and save it to the `llamafile` directory. The model should be in the format of llamafile. You can find models on [llamafile repo](https://github.com/Mozilla-Ocho/llamafile).

#### Create a config file
```bash
cp config.example.jsonc config.jsonc
```

Put the downloaded model name in the `llamafile` field of the config file.
Ex:

```json
{
	"llamafile": "llamafile/DeepSeek-R1-Distill-Llama-8B-Q4_K_M.llamafile",
	...
}
```

If you need any extra system messages, just add it to the `system_messages` array in the config file.


Check if the other configs are matching with your environment too.


## How to create custom commands:

- Create a class inside `commands` path that implements the interface `interfaces\Command`
- Implement the `execute` method
- Use `attributes\Description` attribute to describe the command. It is REALLY IMPORTANT to be underestood as a custom command, the more clear the description and the class name, the better.

Example:
```php
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

```

## Usage
```
wlopes@WLopes:~/Project$ php main.php

  - Enter your command here:
Hey!
>>> Hello! How can I assist you today?

  - Enter your command here:
Can you please return my system username?
>>> wlopes

  - Enter your command here:
how to print a 'hello world' in python?
>>> To print 'hello world' in Python, you can use the print function. Here's how:

print('hello world')

```

In the snippet above we can se a normal AI response and also a custom command returning the system username.

## TODO
- Add more default commands
- Add a custom path that will be git ignored for user commands
- Change how it is handling llamafile to allow it to connect to other APIs too
- Voice commands one day? :eyes: