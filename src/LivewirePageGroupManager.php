<?php

namespace Rahmanramsi\LivewirePageGroup;

class LivewirePageGroupManager
{
    /**
     * @var array<string, PageGroup>
     */
    protected array $pageGroups = [];

    protected ?PageGroup $currentPageGroup = null;

    protected bool $isCurrentPageGroupBooted = false;

    public function getCurrentPageGroup(): ?PageGroup
    {
        return $this->currentPageGroup;
    }

    public function bootCurrentPageGroup(): void
    {
        $pageGroup = $this->getCurrentPageGroup();

        if ($this->isCurrentPageGroupBooted || ! $pageGroup) {
            return;
        }

        $pageGroup->boot();

        $this->isCurrentPageGroupBooted = true;
    }

    public function setCurrentPageGroup(PageGroup $pageGroup): void
    {
        if ($this->currentPageGroup !== $pageGroup) {
            $this->isCurrentPageGroupBooted = false;
        }

        $this->currentPageGroup = $pageGroup;
    }

    public function getPageGroup(?string $id = null): ?PageGroup
    {
        return $this->pageGroups[$id] ?? null;
    }

    /**
     * @return array<string, PageGroup>
     */
    public function getPageGroups(): array
    {
        return $this->pageGroups;
    }

    public function registerPageGroup(PageGroup $pageGroup): void
    {
        $this->pageGroups[$pageGroup->getId()] = $pageGroup;

        $pageGroup->register();
    }
}
