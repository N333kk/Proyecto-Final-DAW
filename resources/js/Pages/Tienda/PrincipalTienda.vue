<script setup>

import { Link } from '@inertiajs/vue3'

defineProps({
    articulos: Object,
    addToCart(articuloId) {
        this.$inertia.post('/cart', { articulo_id: articuloId, cantidad: 1 });
    }
});



</script>

<template>
    <Head title="Welcome" />
    <div class="bg-gray-50 text-black/50 dark:bg-zinc-800 dark:text-white/50">
        <div class="flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                <header class="min-w-full">
                    <nav class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-neutral-600">
                            <div class="space-x-4">
                            <Link href="/tienda" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-white/70">Inicio</Link>
                            <Link href="/articulos" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-white/70">Articulos</Link>
                            <Link href="/pedidos" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-white/70">Pedidos</Link>
                            <Link href="/contacto" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-white/70">Contacto</Link>
                        </div>
                        <Link href="/cart">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </Link>
                    </nav>
                </header>

                <main class="mt-6">
                    <div class="min-h-screen min-w-screen flex flex-col items-center justify-center">
                        <h1 class="text-4xl font-bold text-center">Ultimos Articulos</h1>
                        <p class="mt-4 text-center text-lg text-black dark:text-white/70">
                            Aqui podras encontrar los articulos mas recientes.
                        </p>
                        <ul class="p-8 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">

                            <li class="bg-stone-300 p-6 rounded-lg shadow-md flex text-black" v-for="articulo in articulos" :key="articulo.id">
                                <div class="flex-shrink-0 p-1">
                                    <img class="w-24 h-36 object-cover rounded" :src="`${articulo.imagen}`" alt="Imagen articulo">
                                </div>
                                <div class="ml-4">
                                    <h2 class="text-lg font-semibold mb-2">{{ articulo.nombre }}</h2>
                                    <p class="text-neutral-900 font-bold">{{ articulo.categoria }}</p>
                                    <p class="text-neutral-900 font-semibold">{{ articulo.precio }} €</p>
                                    <Link :href="`/articulos/${articulo.id}`" class="bg-zinc-800 hover:bg-zinc-500 text-white font-bold py-2 px-4 rounded mt-2 mx-1 inline-block">Mostrar Articulo</Link>
                                    <Link :href="`/cart/${articulo.id}`" method="POST" class="bg-zinc-800 hover:bg-zinc-500 text-white font-bold py-2 px-4 rounded mt-2 mx-1 inline-block" @click="addToCart(articulo.id)">Añadir al Carrito</Link>
                                </div>
                            </li>
                        </ul>

                    </div>
                </main>

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">

                </footer>
        </div>
    </div>
</template>
