<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header container
        class="border border-transparent bg-[#0054a3] dark:border-zinc-700 dark:bg-zinc-900 lg:rounded-2xl lg:mx-24 lg:mt-2">
        <flux:sidebar.toggle class="lg:hidden text-white/80!" icon="bars-2" inset="left" />

        <a href="{{ route('dashboard') }}" class="ms-2 me-16 flex items-center space-x-2 rtl:space-x-reverse lg:ms-0"
            wire:navigate>
            <x-app-logo />
        </a>

        <flux:navbar class="-mb-px max-lg:hidden">
            <!-- Dashboard -->
            <flux:navbar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                class="text-white/80! {{ request()->routeIs('dashboard')
                    ? 'bg-primary-300/40! border-transparent! data-current:after:bg-transparent'
                    : '' }}"
                wire:navigate>
                {{ __('Dashboard') }}
            </flux:navbar.item>
            <!-- Project Management -->
            <flux:navbar.item icon="folder" :href="route('projects')"
                :current="request()->routeIs('projects') || request()->routeIs('projects.*')"
                class="text-white/80! {{ request()->routeIs('projects') || request()->routeIs('projects.*')
                    ? 'bg-primary-300/40! border-transparent! data-current:after:bg-transparent'
                    : '' }}"
                wire:navigate>
                {{ __('Program') }}
            </flux:navbar.item>
            <!-- User Management -->
            <flux:navbar.item icon="users" :href="route('users')"
                :current="request()->routeIs('users') || request()->routeIs('users.*')"
                class="text-white/80! {{ request()->routeIs('users') || request()->routeIs('users.*')
                    ? 'bg-primary-300/40! border-transparent! data-current:after:bg-transparent'
                    : '' }}"
                wire:navigate>
                {{ __('Pengguna') }}
            </flux:navbar.item>
            <!-- Resident Management -->
            <flux:navbar.item icon="user-group" :href="route('residents')"
                :current="request()->routeIs('residents') || request()->routeIs('residents.*')"
                class="text-white/80! {{ request()->routeIs('residents') || request()->routeIs('residents.*')
                    ? 'bg-primary-300/40! border-transparent! data-current:after:bg-transparent'
                    : '' }}"
                wire:navigate>
                {{ __('Warga') }}
            </flux:navbar.item>
        </flux:navbar>

        <flux:spacer />

        <!-- Desktop User Menu -->
        <flux:dropdown position="top" align="end">
            <flux:profile class="cursor-pointer text-white/80!" :initials="auth()->user()->initials()" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                <span
                                    class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                    {{ auth()->user()->initials() }}
                                </span>
                            </span>

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <span class="truncate font-semibold">{{ auth()->user()->full_name }}</span>
                                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full"
                        data-test="logout-button">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    <!-- Mobile Menu -->
    <flux:sidebar stashable sticky class="lg:hidden bg-[#0054a3] border-e dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

        <a href="{{ route('dashboard') }}" class="ms-1 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
            <x-app-logo />
        </a>

        <flux:navlist variant="outline">
            <flux:navlist.group>
                <!-- Dashboard -->
                <flux:navlist.item icon="layout-grid" :href="route('dashboard')"
                    :current="request()->routeIs('dashboard')"
                    class="text-white/80! {{ request()->routeIs('dashboard')
                        ? 'bg-primary-300/40! border-transparent! data-current:after:bg-transparent'
                        : '' }}"
                    wire:navigate>
                    {{ __('Dashboard') }}
                </flux:navlist.item>
                <!-- Project Management -->
                <flux:navlist.item icon="folder" :href="route('projects')"
                    :current="request()->routeIs('projects') || request()->routeIs('projects.*')"
                    class="text-white/80! {{ request()->routeIs('projects') || request()->routeIs('projects.*')
                        ? 'bg-primary-300/40! border-transparent! data-current:after:bg-transparent'
                        : '' }}"
                    wire:navigate>
                    {{ __('Program') }}
                </flux:navlist.item>
                <!-- User Management -->
                <flux:navlist.item icon="users" :href="route('users')"
                    :current="request()->routeIs('users') || request()->routeIs('users.*')"
                    class="text-white/80! {{ request()->routeIs('users') || request()->routeIs('users.*')
                        ? 'bg-primary-300/40! border-transparent! data-current:after:bg-transparent'
                        : '' }}"
                    wire:navigate>
                    {{ __('Pengguna') }}
                </flux:navlist.item>
                <!-- Resident Management -->
                <flux:navlist.item icon="user-group" :href="route('residents')"
                    :current="request()->routeIs('residents') || request()->routeIs('residents.*')"
                    class="text-white/80! {{ request()->routeIs('residents') || request()->routeIs('residents.*')
                        ? 'bg-primary-300/40! border-transparent! data-current:after:bg-transparent'
                        : '' }}"
                    wire:navigate>
                    {{ __('Warga') }}
                </flux:navlist.item>
            </flux:navlist.group>
        </flux:navlist>
    </flux:sidebar>

    {{ $slot }}

    <!-- Global Alert Component -->
    @livewire('components.global-alert')

    @fluxScripts
</body>

</html>
