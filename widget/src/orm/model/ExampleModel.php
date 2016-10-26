<?php

namespace ride\wheel\orm\model;

use ride\library\event\EventManager;
use ride\library\i18n\I18n;
use ride\library\orm\entry\Entry;
use ride\library\orm\model\GenericModel;

class ExampleModel extends GenericModel {

    /**
     * @return I18n
     */
    private function getI18n() {
        return $this->getOrmManager()->getDependencyInjector()->get(I18n::class);
    }

    /**
     * @return EventManager
     */
    private function getEventManager() {
        return $this->getOrmManager()->getDependencyInjector()->get(EventManager::class);
    }

    /**
     * @param Entry $entry
     *
     * @return null|void
     */
    protected function saveEntry($entry) {
        $isNew = $entry->getEntryState() === Entry::STATE_NEW;

        // Set the locale
        if (!$entry->getLocale()) {
            $entry->setLocale($this->getI18n()->getLocale()->getCode());
        }

        parent::saveEntry($entry);

        // Trigger events after saving. An event could eg. log extra information or send mails.
        $eventManager = $this->getEventManager();
        if ($isNew) {
            $eventManager->triggerEvent('example.create', [
                'entry' => $entry,
            ]);
        } else {
            $eventManager->triggerEvent('example.update', [
                'entry' => $entry,
            ]);
        }

        return;
    }

}
