# DDEV for Shopware applications

If you haven't installed DDEV yet, see [this Confluence page](https://lets-talk.atlassian.net/wiki/spaces/SD/pages/281640962/DDEV).

## Adding to a project

Run:

```bash
composer require --dev letstalk/sw-ddev-application-config
```

This will ask you a few questions:

* The name of your project. Use something that is unique, short, but descriptive. Only letters, numbers and dashes are allowed.
* The port for the database and Redis server to expose to the host. Make sure they are globally unique across all projects. The tool will suggest a random number by default, which is hopefully unique.

After this is done, a `.ddev` folder will have been created.
You should commit this into git.

## Usage

* [Basic usage](./docs/basic-usage.md): how to use DDEV for your project.
* [Commands](./docs/commands.md): available commands and how to create custom ones.
* [Updating](./docs/updating.md): information on updating DDEV.
* [Symlinking dependencies](./docs/symlinking-dependencies.md): how to symlink internal dependencies for development.
