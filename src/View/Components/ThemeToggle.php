<?php

namespace Mary\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ThemeToggle extends Component
{
    public string $uuid;

    public function __construct(
        public ?string $id = null,
        public ?string $value = null,
        public ?string $light = "Light",
        public ?string $dark = "Dark",
        public ?string $lightTheme = "light",
        public ?string $darkTheme = "dark",
        public ?string $lightClass = "light",
        public ?string $darkClass = "dark",
        public ?bool $withLabel = false,

    ) {
        $this->uuid = "mary" . md5(serialize($this)) . $id;
    }

    public function render(): View|Closure|string
    {
        return <<<'HTML'
                <div>
                    <label
                        for="{{ $uuid }}"
                        x-data="{
                            theme: $persist(window.matchMedia('(prefers-color-scheme: dark)').matches ? '{{ $darkTheme }}' : '{{ $lightTheme }}').as('mary-theme'),
                            class: $persist(window.matchMedia('(prefers-color-scheme: dark)').matches ? '{{ $darkClass }}' : '{{ $lightClass }}').as('mary-class'),
                            init() {
                                if (this.theme == '{{ $darkTheme }}') {
                                    this.$refs.sun.classList.add('swap-off');
                                    this.$refs.sun.classList.remove('swap-on');
                                    this.$refs.moon.classList.add('swap-on');
                                    this.$refs.moon.classList.remove('swap-off');
                                }
                                this.setToggle()
                            },
                            setToggle() {
                                document.documentElement.setAttribute('data-theme', this.theme)
                                document.documentElement.setAttribute('class', this.class)
                                this.$dispatch('theme-changed', this.theme)
                                this.$dispatch('theme-changed-class', this.class)
                            },
                            toggle() {
                                this.theme = this.theme == '{{ $lightTheme }}' ? '{{ $darkTheme }}' : '{{ $lightTheme }}'
                                this.class = this.theme == '{{ $lightTheme }}' ? '{{ $lightClass }}' : '{{ $darkClass }}'
                                this.setToggle()
                            }
                        }"
                        @mary-toggle-theme.window="toggle()"
                        {{ $attributes->class("swap swap-rotate") }}
                    >
                        <input id="{{ $uuid }}" type="checkbox" class="theme-controller opacity-0" @click="toggle()" :value="theme" />
                        <x-mary-icon x-ref="sun" name="o-sun" class="swap-on" />
                        <x-mary-icon x-ref="moon" name="o-moon" class="swap-off"  />
                    </label>
                </div>
                <script>
                    document.documentElement.setAttribute("data-theme", localStorage.getItem("mary-theme")?.replaceAll("\"", ""))
                    document.documentElement.setAttribute("class", localStorage.getItem("mary-class")?.replaceAll("\"", ""))
                </script>
            HTML;
    }
}
