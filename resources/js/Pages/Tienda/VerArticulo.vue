<script setup>

import { Link } from '@inertiajs/vue3'

defineProps({
    articulo: Object,
});

</script>

<template>
    <div class="bg-gray-50 text-black/50 dark:bg-zinc-800 dark:text-white/50 min-h-screen">
        <div class="flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                <header class="min-w-full">
                        <nav class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-neutral-600">
                            <div class="space-x-4">
                            <Link href="/tienda" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-white/70">Inicio</Link>
                            <Link href="/articulos" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-white/70">Articulos</Link>
                            <Link href="/contacto" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-white/70">Contacto</Link>
                        </div>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
</svg>


                        </nav>
                </header>

                <main class="mt-6 ">
                    <div class="bg-stone-300 p-8  mx-64 grid grid-cols-2 gap-2 grid-rows-3 rounded-xl">
                        <div>
                        <h1 class="text-zinc-800 text-4xl font-bold px-2">{{ articulo.nombre }}</h1>
                        <p class="text-zinc-800 font-bold px-2">{{ articulo.categoria }}</p>
                        </div>
                        <div class="row-start-2 row-end-2 px-2">
                            <img class="w-48 h-48 object-cover rounded" :src="`${articulo.imagen}`" alt="Imagen articulo">
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
