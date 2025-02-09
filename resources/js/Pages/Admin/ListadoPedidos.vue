<script setup>
import { Link, router } from '@inertiajs/vue3'

const logout = () => {
    router.post(route('logout'));
};


defineProps({
    pedidos: Array,
    usuarios: Array
});

</script>

<template>

    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white  min-h-screen">
        <div class="flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <header class="min-w-full">
                <nav
                    class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-black text-white dark:text-white/80 border-b border-white/20">
                    <div class="space-x-4">
                        <Link href="/tienda" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">
                        Inicio</Link>
                        <Link href="/articulos" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">
                        Articulos</Link>
                        <Link v-if="$page.props.auth.user" href="/pedidos"
                            class="text-sm font-medium  hover:text-black dark:hover:text-white/50">Pedidos</Link>
                        <Link v-if="$page.props.auth.user" href="/perfil"
                            class="text-sm font-medium  hover:text-black dark:hover:text-white/50">{{
                                $page.props.auth.user.name }}</Link>
                        <template v-else>
                            <Link href="/login" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">
                            Log in
                            </Link>
                            <Link href="/register"
                                class="text-sm font-medium  hover:text-black dark:hover:text-white/50">
                            Register
                            </Link>
                        </template>
                    </div>
                    <div class="flex">
                        <form v-if="$page.props.auth.user" @submit.prevent="logout">
                            <button type="submit"
                                class="text-sm font-medium px-4 text-white dark:text-white/80 hover:text-white/50 dark:hover:text-white/50">
                                Log Out
                            </button>
                        </form>
                        <Link v-if="$page.props.auth.user" href="/cart">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 dark:stroke-white dark:hover:stroke-white/50">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        </Link>
                    </div>
                </nav>
            </header>
            <div class="p-8">
                <h2 class="p-4 border border-slate-700 rounded-xl bg-slate-800">Listado de pedidos</h2>
                <ul class="p-8 flex flex-col w-full">
                    <li class="bg-white p-6 rounded-lg shadow-md flex items-center text-black mb-2">
                        <h2 class="text-lg font-semibold flex-1">Id de pedido</h2>
                        <p class="text-blue-700 font-bold flex-1">Estado de pedido</p>
                        <p class="text-blue-700 flex-1">Direccion de envio</p>
                        <p class="text-blue-700 flex-1">Usuario</p>
                        <p class="text-blue-700 flex-1">Acciones</p>
                    </li>

                    <li class="bg-white p-6 rounded-lg shadow-md flex items-center text-black mb-2"
                        v-for="pedido in pedidos" :key="pedido.id">
                        <h2 class="text-lg font-semibold flex-1">{{ pedido.id }}</h2>
                        <p class="text-blue-700 font-bold flex-1">{{ pedido.estado }}</p>
                        <p class="text-blue-700 flex-1">{{ pedido.direccion_envio }}</p>
                        <p class="text-blue-700 flex-1">{{ pedido.user.name }}</p>
                        <div class="flex items-center space-x-4 flex-1">
                            <Link v-if="$page.props.auth.user.rol === 'admin'" :href="`/pedidos/${pedido.id}/edit`"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Editar
                            </Link>
                            <Link
                                v-if="$page.props.auth.user.rol === 'admin' || $page.props.auth.user.id === pedido.user_id & pedido.estado === 'Pendiente'"
                                :href="`/pedidos/${pedido.id}`" method="delete"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">Cancelar
                            </Link>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
