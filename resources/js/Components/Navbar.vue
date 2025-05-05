<script setup>
import { Link, router } from '@inertiajs/vue3';

// Lógica para cerrar sesión
const logout = () => {
    router.post(route('logout'));
};

// El conteo de elementos del carrito viene ahora compartido desde Inertia en todas las páginas
// No necesitamos recibirlo como prop, podemos accederlo directamente desde $page.props
</script>

<template>
    <header class="min-w-full sticky top-0 z-50">
        <nav
            class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-white/95 dark:bg-black/95 backdrop-blur-sm text-gray-800 dark:text-white/80 border-b border-gray-200 dark:border-white/20 shadow-lg">
            <div class="space-x-4">
                <Link href="/" class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                Inicio</Link>
                <Link href="/articulos" class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                Articulos</Link>
                <Link v-if="$page.props.auth.user" href="/pedidos" class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                Pedidos</Link>
                <Link v-if="$page.props.auth.user" href="/perfil" class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">{{
                    $page.props.auth.user.name }}</Link>
                <template v-else>
                    <Link href="/login" class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                        Log in
                    </Link>
                    <Link href="/register" class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                        Register
                    </Link>
                </template>
                <Link v-if="$page.props.auth.user && $page.props.auth.user.rol == 'admin'" href="/dashboard" class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                    Dashboard
                </Link>
            </div>
            <div class="flex items-center gap-3">
                <form v-if="$page.props.auth.user" @submit.prevent="logout">
                    <button type="submit"
                        class="text-sm font-medium px-4 text-gray-700 dark:text-white/80 hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                        Log Out
                    </button>
                </form>
                <Link v-if="$page.props.auth.user" href="/cart" class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="cart-icon size-6 stroke-gray-700 hover:stroke-purple-600 dark:stroke-white dark:hover:stroke-purple-400 transition-colors">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <span v-if="$page.props.cartItemsCount > 0" class="absolute -top-2 -right-2 bg-purple-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                        {{ $page.props.cartItemsCount }}
                    </span>
                </Link>
            </div>
        </nav>
    </header>
</template>
