# Commands

DDEV supports creating custom commands that you can run without typing them out in full.
You can create command files in `.ddev/commands/web/[somefile]` and then you can run them with
`ddev [somefile]`. Make sure the files are executable.

## Available commands

Below is a list of useful commands to run for Shopware:

* `ddev install`: Setup your installation and application. Will update your system configuration in the database. 
    If you remove `install.lock` from your project, it will drop your database and reinstall.
* `ddev exec bin/console es:index`: Create the Storefront ES index.
* `ddev exec bin/console theme:change Storefront --all`: Set the default theme for all sales channels.
* `ddev demodata`: load demodata.
* `ddev clear-cache [env]`: clear the application cache and all cache pools. Optionally pass a specific environment to clear for.
* `ddev clear-cache --force`: force clear the var/cache directory
* `ddev build -a`: build the admin javascript/CSS.
* `ddev build -s`: build the storefront javascript/CSS.
* `ddev watch -a`: watch the admin javascript/CSS.
* `ddev watch -s`: watch the storefront javascript/CSS.
* `ddev exec curl -XDELETE elasticsearch:9200/_all`: clear elasticsearch database.
* `ddev queue`: start the messenger consumer for Shopware projects.

## Passing arguments to commands

Commands itself can have arguments. To see what they are, you can run a command with the `-h` flag.

If the command runs another command internally and you want to pass arguments to that inner command, prefix them with `--`.
For example:

```bash
ddev exec demodata -- --products=10
```
