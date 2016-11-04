<?php

namespace Capriolo\Eavquent\Events;

use Capriolo\Eavquent\Attribute\Manager;
use Illuminate\Container\Container;

class AttributeWasSaved
{
    /**
     * Refresh cache when saving attributes.
     */
    public function handle()
    {
        $manager = $this->getManager();

        // Anytime an attribute is saved (updated or just created) we will refresh
        // the attribute cache. This way we'll make sure we are not working with
        // with outdated attribute options or even that do not exist anymore.
        $manager->refresh();
    }

    /**
     * Get the manager instance.
     *
     * @return Manager
     */
    protected function getManager()
    {
        return Container::getInstance()->make(Manager::class);
    }
}
