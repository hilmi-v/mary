<?php

namespace Mary\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MenuSub extends Component
{
    public string $uuid;

    public function __construct(
        public ?string $title = null,
        public ?string $icon = null,
        public bool $open = false,
        public ?bool $enabled = true,
    ) {
        $this->uuid = "mary" . md5(serialize($this));
    }

    public function render(): View|Closure|string
    {
        if ($this->enabled === false) {
            return '';
        }

        return <<<'HTML'
                @aware(['activeBgColor' => 'bg-base-300'])

                @php
                    $submenuActive = Str::contains($slot, 'mary-active-menu');
                @endphp

                <li
                    x-data="
                    {
                        show: @if($submenuActive || $open) true @else false @endif,
                        toggle(){
                            // From parent Sidebar
                            if (this.collapsed) {
                                this.show = true
                                $dispatch('menu-sub-clicked');
                                return
                            }

                            this.show = !this.show
                        }
                    }"
                >
                    <details :open="show" @if($submenuActive) open @endif @click.stop>
                        <summary @click.prevent="toggle()"
                         {{
                            $attributes->class([
                                "hover:text-inherit px-4 py-1.5 my-0.5 text-inherit",
                                "mary-active-menu $activeBgColor" => $submenuActive
                            ])
                        }}
                         >
                            @if($icon)
                                <x-mary-icon :name="$icon" class="inline-flex my-0.5"  />
                            @endif

                            <span class="mary-hideable whitespace-nowrap truncate">{{ $title }}</span>
                        </summary>

                        <ul class="mary-hideable">
                            {{ $slot }}
                        </ul>
                    </details>
                </li>
            HTML;
    }
}
