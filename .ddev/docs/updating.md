# Updating DDEV

You should never update DDEV settings in a project directly, since we use
a Symfony Flex recipe for this. The recipes can be found [here](https://gitlab.ops.letstalk.nl/development-operations/flex-recipes).

Note that we try to keep the DDEV setup in sync with versions running on production,
so you should only update DDEV if you are sure that the production environment is also updated.
