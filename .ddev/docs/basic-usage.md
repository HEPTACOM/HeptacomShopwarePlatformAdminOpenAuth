# Using DDEV

To start DDEV for a project, run `ddev start`.

Note: make sure the DevDocker (Windows) or Valet, MySQL, Redis and Elasticsearch are stopped before running this. For macOS you can run:

```bash
valet stop
for i in mailhog mariadb redis; do brew services stop $i; done
cd [folder of elasticsearch]
docker compose stop
```

You can use `ddev describe` to see all running services of a project with their URL’s or ports.
`ddev launch` will open your default browser on the homepage of the project.

You can use `ddev ssh` to connect to the web container to run any command you want.
Using `ddev exec [some command]` you can run commands directly
and `ddev composer [args]` will run composer with those arguments.

For PHPStorm, we recommend installing the [DDEV plugin](https://plugins.jetbrains.com/plugin/18813-ddev-integration).
This will auto configure your database, PHP version, xdebug, etc. See also the [documentation on the DDEV site](https://ddev.readthedocs.io/en/stable/users/install/phpstorm/)
for this.

On Windows, you need to make sure your projects are inside your WSL and not inside a folder on Windows. i.e. they need to be in `/home/<user>`.

Also on Windows, we recommend to use “Remote development” with WSL 2, since this is faster than local development. Note that you might need to install some plugins either in the ‘remote’ PHPStorm or in the host instance.

Note: this requires you to run `ddev config global --xdebug-ide-location=wsl2` once.

If you ever need to stop all DDEV services (because you need to start the old Docker / Valet again), you can run `ddev poweroff`.

## Adding extra hostnames

Some projects require more than one hostname (for example the MediaMarkt sales portals).
You can add extra hostnames by editing `.ddev/config.yaml` and adding an array called `additional_hostnames`.
Any hostname you put in, appended with `.ddev.site`, will be available to your application.
Make sure to run `ddev restart` after making any changes.

## Mailing

We use Mailpit to capture any emails.
Use `ddev describe` to get the URL of the Mailpit web interface.

## XDebug

Run `ddev xdebug on` to enable XDebug.
Then listen for connections in PHPStorm and add a breakpoint.
That is all, it’s like magic 🎉.
Run `ddev xdebug off` to disable XDebug again (it has a performance overhead to keep it running).

If you have multiple storefronts you want to debug, you should add the extra host as a server in PHPStorm
through PHP → Servers → Copy the default ddev site and rename the host.

## Blackfire

To use the Blackfire profiler, first run `ddev blackfire on`.
Now you can use any of these methods to profile any request or command.
Be sure to run `ddev blackfire off` to disable Blackfire again.

Some examples to profile using the CLI:

* `ddev exec blackfire curl https://your-host.ddev.site/admin`
* `ddev exec blackfire run bin/console cache:clear`

Note: XDebug and Blackfire can't run at the same time.
DDEV will disable XDebug when enabling Blackfire, but not the other way around.
Make sure to run `ddev blackfire off` to disable Blackfire before starting XDebug.
