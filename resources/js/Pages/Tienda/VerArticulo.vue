<script setup>

import { Link, router } from '@inertiajs/vue3'

const logout = () => {
    router.post(route('logout'));
};


defineProps({
    articulo: Object,
});

</script>

<template>
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white  min-h-screen">
        <div class="flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                <header class="min-w-full">
                    <nav class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-black text-white dark:text-white/80 border-b border-white/20">
                            <div class="space-x-4">
                                <Link href="/tienda" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">Inicio</Link>
                            <Link href="/articulos" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">Articulos</Link>
                            <Link v-if="$page.props.auth.user" href="/pedidos" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">Pedidos</Link>
                            <Link v-if="$page.props.auth.user" href="/perfil" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">{{ $page.props.auth.user.name }}</Link>
                            <template v-else>
                            <Link
                                href="/login"
                                class="text-sm font-medium  hover:text-black dark:hover:text-white/50"
                            >
                                Log in
                            </Link>
                            <Link
                                href="/register"
                                class="text-sm font-medium  hover:text-black dark:hover:text-white/50"
                            >
                                Register
                            </Link>
                        </template>
                        </div>
                        <div class="flex">
                            <form v-if="$page.props.auth.user" @submit.prevent="logout">
                                            <button type="submit" class="text-sm font-medium px-4 text-white dark:text-white/80 hover:text-white/50 dark:hover:text-white/50">
                                                Log Out
                                            </button>
                                        </form>
                        <Link v-if="$page.props.auth.user" href="/cart">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 dark:stroke-white dark:hover:stroke-white/50">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </Link>
                    </div>
                    </nav>
                </header>

                <main class="mt-6 ">
                    <div class="bg-stone-300 p-8  mx-64 grid grid-cols-2 gap-2 grid-rows-3 rounded-xl">
                        <div>
                        <h1 class="text-zinc-800 text-4xl font-bold px-2">{{ articulo.nombre }}</h1>
                        <p class="text-zinc-800 font-bold px-2">{{ articulo.categoria && articulo.categoria.length > 0
     ? articulo.categoria[0].nombre
     : 'Sin categoría' }}</p>
                        </div>
                        <div class="row-start-2 row-end-2 px-2">
                            <img class="w-48 h-48 object-cover rounded" :src="articulo.imagenes && articulo.imagenes.length > 0
              ? `/storage/${articulo.imagenes[0].ruta}`
              : '/img/placeholder.webp'" alt="Imagen articulo">
                        </div>
                        <div class="row-start-2 row-end-3 col-span-2">

                            <p class="text-zinc-700">{{ articulo.descripcion }}</p>
                            <p class="text-zinc-900 font-semibold">{{ articulo.precio }} €</p>
                        </div>

                    </div>
                </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">

                </footer>
        </div>
    </div>
</template>
