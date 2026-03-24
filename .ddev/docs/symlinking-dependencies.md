# Symlinking dependencies

Often times, your plugin will depend on other internal projects you are developing.

These dependencies are installed through composer, but you want to be able to develop them in parallel with your plugin.

To do this, it is possible to 'symlink' these dependencies into your plugin.

**NOTE**: do not commit any of the changes you make for this process to the repository!

## DDEV volume mounts

Firstly, we need to let DDEV know what other project should be available in the container.
We do this by adding a volume mapping as follows:

```yaml
# .ddev/docker-compose.mounts.yaml
services:
  web:
    volumes:
      - "/Users/youruser/Repositories/yourproject:/var/www/yourproject"
```

When restarting DDEV, the project will be available in the container under `/var/www/yourproject`,
next to the original `/var/www/html` which contains your root project.

## Composer dependency

Now that your local checkout of the dependant project is available in the DDEV web container,
composer should be made aware that it should symlink the project instead of downloading it from the remote.

This can be done by adding an extra repositories entry:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "/var/www/yourproject"
    }
  ]
}
```

After this has been added, you can update the dependency, and it will make a local symlink in the vendor directory.
As this is a symlink within the container, it will not go to any logical place when you try to open it on PHPStorm.

You can however [attach the project](https://www.jetbrains.com/help/phpstorm/open-close-and-move-projects.html) to your
root project in PHPStorm and edit the code and it will be used for autocompletion.

Do this by going to `File > Attach project...` and selecting the project directory.

## PHPStorm Xdebug

When attaching the project to your root project, PHPStorm is not aware
that your extra projects are available in the DDEV environment as well.

In order to make it known, you should edit the mapping of the server.

Assuming you have installed the DDEV plugin for PHPStorm, you can do the following:

- Go to `Settings > PHP > Servers` and select the DDEV site environment.
- Under `Project files`, your root project should already be mapped to `/var/www/html`.
  Enter the appropriate `/var/www/yourproject` in the attached project.

