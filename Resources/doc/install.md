# composer.json - install bundle

Todo: separate bundle into its own repository and load it via composer.

# AppKernel.php - register bundle

Add the following line into `$bundles` array:

    new Innobyte\TokenBundle\InnobyteTokenBundle(),

# config.yml - Add bundle mapping

Add the mapping for the bundle under an entity manager (here "default")

    doctrine:
        orm:
            entity_managers:
                default:
                    mappings:
                        InnobyteTokenBundle: ~

# paramaters.yml - Add the entity manager name

Add the entity manager name (here "local") - the one you put the mapping under in the above step.
If none is provided, "default" will be used.

    innobyte_token:
        entity_manager: local
