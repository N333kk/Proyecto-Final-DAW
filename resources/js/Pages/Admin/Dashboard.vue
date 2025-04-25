<script setup>

import { Head, Link, router } from '@inertiajs/vue3'

const logout = () => {
    router.post(route('logout'));
};

const props = defineProps({
    users: Object,
    articulos: Object,
    pedidos: Object,
});

</script>

<template>
    <Head title="Admin Dashboard" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white  min-h-screen">
        <div class="flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <header class="min-w-full">
                <nav
                    class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-black text-white dark:text-white/80 border-b border-white/20">
                    <div class="space-x-4">
                        <Link href="/" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">
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
                        <Link v-if="$page.props.auth.user.rol == 'admin'" href="/dashboard"
                            class="text-sm font-medium  hover:text-black dark:hover:text-white/50">Dashboard</Link>
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

            <div class="w-full">
                <div class="py-4 border-b border-b-white/20 bg-black">
                    <h2 class="text-white py-2 px-4 font-bold text-4xl">Dashboard</h2>
                </div>
                <div class=" text-white rounded-xl bg-black mt-4 mx-8">
                    <div
                        class="text-white py-3 px-4 border-b border-l border-r border-t rounded-tl-lg rounded-tr-lg border-white/25 bg-gradient-to-r from-black to-white/15">
                        <h2 class="font-bold text-1xl">Listado Usuarios</h2>
                    </div>
                    <div
                        class="flex items-center text-white py-3 px-4 border-b border-l border-r border-white/25 bg-gradient-to-r from-black to-white/15">
                        <h2 class="px-2 font-bold text-1xl flex-1 my-4">Nombre</h2>
                        <h2 class="font-bold text-1xl flex-1 my-4  px-4">Telefono</h2>
                        <h2 class="font-bold text-1xl flex-1 my-4 px-4">Direccion</h2>
                        <h2 class="font-bold text-1xl flex-1 my-4 px-4">Direccion Facturacion</h2>
                        <h2 class="font-bold text-1xl flex-1 my-4 px-4">Rol</h2>
                    </div>
                    <ul class=" border-b border-l border-r mb-8 border-white/25 rounded-br-lg rounded-bl-lg ">
                        <li v-for="(user, index) in users" :key="user.id"
                            :class="{ 'border-b border-white/30': index !== users.length - 1 }"
                            class="flex items-center bg-gradient-to-r from-black to-white/15">
                            <span class="px-4 font-semibold flex-1 my-4"> {{ user.name }} </span>
                            <span class="font-semibold flex-1 my-4 px-4"> {{ user.telefono }}</span>
                            <span class="font-semibold flex-1 my-4 px-4"> {{ user.direccion_envio }}</span>
                            <span class="font-semibold flex-1 my-4 px-4"> {{ user.direccion_facturacion }}</span>
                            <span class="font-semibold flex-1 my-4 px-4"> {{ user.rol }}</span>
                        </li>
                    </ul>

                    <div
                        class="text-white py-3 px-4 border-b border-l border-r border-t rounded-tl-lg rounded-tr-lg border-white/25 bg-gradient-to-r from-black to-white/15">
                        <h2 class="font-bold text-1xl">Listado Articulos</h2>
                    </div>
                    <div
                        class="flex items-center text-white py-3 px-4 border-b border-l border-r border-white/25 bg-gradient-to-r from-black to-white/15">
                        <h2 class="px-2 font-bold text-1xl flex-1 my-4">Nombre</h2>
                        <h2 class="font-bold text-1xl flex-1 my-4  px-4">Categoria</h2>
                        <h2 class="font-bold text-1xl flex-1 my-4 px-4">Descripcion</h2>
                        <h2 class="font-bold text-1xl flex-1 my-4 px-4">Precio</h2>
                        <h2 class="font-bold text-1xl flex-1 my-4 px-4">Ultima Actualizacion</h2>
                    </div>
                    <ul class=" border-b border-l border-r mb-8 border-white/25 rounded-br-lg rounded-bl-lg ">
                        <li v-for="(articulo, index) in articulos" :key="articulo.id"
                            :class="{ 'border-white/30 border-b rounded-br-lg rounded-bl-lg': index !== articulos.length - 1 }"
                            class="flex items-center bg-gradient-to-r from-black to-white/15">
                            <span class="px-4 font-semibold flex-1 my-4"> {{ articulo.nombre }} </span>
                            <span class="font-semibold flex-1 my-4 px-4"> {{ articulo.categoria }}</span>
                            <span class="font-semibold flex-1 my-4 px-4"> {{ articulo.descripcion }}</span>
                            <span class="font-semibold flex-1 my-4 px-4"> {{ articulo.precio }}</span>
                            <span class="font-semibold flex-1 my-4 px-4"> {{ articulo.updated_at.slice(0,10) }}</span>
                        </li>
                    </ul>

                    <div
                        class="text-white py-3 px-4 border-b border-l border-r border-t rounded-tl-lg rounded-tr-lg border-white/25 bg-gradient-to-r from-black to-white/15">
                        <h2 class="font-bold text-1xl">Listado Pedidos</h2>
                    </div>
                    <div
                        class="flex items-center text-white py-3 px-4 border-b border-l border-r border-white/25 bg-gradient-to-r from-black to-white/15">
                        <h2 class="px-2 font-bold text-1xl flex-1 my-4">Cliente</h2>
                        <h2 class="font-bold text-1xl flex-1 my-4  px-4">Estado</h2>
                        <h2 class="font-bold text-1xl flex-1 my-4 px-4">Direccion</h2>
                        <h2 class="font-bold text-1xl flex-1 my-4 px-4">Ultima Actualizacion</h2>
                    </div>
                    <ul class=" border-b border-l border-r mb-8 border-white/25 rounded-br-lg rounded-bl-lg ">
                        <li v-for="(pedido, index) in pedidos" :key="pedido.id"
                            :class="{ 'border-b border-white/30': index !== pedidos.length - 1 }"
                            class="flex items-center bg-gradient-to-r from-black to-white/15">
                            <span class="px-4 font-semibold flex-1 my-4"> {{ users.find(user => user.id === pedido.user_id)?.name || 'N/A' }} </span>
                            <span class="font-semibold flex-1 my-4 px-4"> {{ pedido.estado }}</span>
                            <span class="font-semibold flex-1 my-4 px-4"> {{ pedido.direccion_envio }}</span>
                            <span class="font-semibold flex-1 my-4 px-4"> {{ pedido.updated_at.slice(0,10) }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>
