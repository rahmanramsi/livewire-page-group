<?php

namespace Rahmanramsi\LivewirePageGroup;

use Closure;
use Rahmanramsi\LivewirePageGroup\Components\Component;
use Rahmanramsi\LivewirePageGroup\PageGroup\Concern;

class PageGroup extends Component
{
  use Concern\HasLivewireComponents,
    Concern\HasMiddleware;

  protected bool $isDefault = false;

  protected ?Closure $bootUsing = null;

  public static function make(): static
  {
    $static = app(static::class);
    $static->configure();

    return $static;
  }

  public function default(bool $condition = true): static
  {
    $this->isDefault = $condition;

    return $this;
  }

  public function boot(): void
  {

    $this->registerRenderHooks();

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

  public function isDefault(): bool
  {
    return $this->isDefault;
  }
}
