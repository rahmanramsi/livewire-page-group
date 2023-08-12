<?php

namespace Rahmanramsi\LivewirePageGroup;

use Closure;
use Rahmanramsi\LivewirePageGroup\Components\Component;
use Rahmanramsi\LivewirePageGroup\PageGroup\Concern;

class PageGroup extends Component
{
    use
        Concern\HasId,
        Concern\HasLivewireComponents,
        Concern\HasMiddleware,
        Concern\HasRoutes,
        Concern\HasLayout,
        Concern\HasHomePage;

    protected ?Closure $bootUsing = null;

    public static function make(): static
    {
        $static = app(static::class);
        return $static;
    }

    public function boot(): void
    {

        $this->registerLivewireComponents();
        $this->registerLivewirePersistentMiddleware();

        if ($callback = $this->bootUsing) {
            $callback($this);
        }
    }

    public function bootUsing(?Closure $callback): static
    {
        $this->bootUsing = $callback;

        return $this;
    }
}
