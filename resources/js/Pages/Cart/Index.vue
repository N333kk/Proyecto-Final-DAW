<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  cartItems: Array
});

const precioTotal = computed(() => {
  return props.cartItems.reduce((sum, item) => sum + (item.cantidad * item.articulo.precio), 0).toFixed(2);
});


</script>

<template>
    <div class="bg-gray-50 text-black/50 dark:bg-zinc-800 dark:text-white/50  min-h-screen">
        <div class="flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white ">
        <header class="min-w-full">
                    <nav class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-neutral-600">
                            <div class="space-x-4">
                            <Link href="/tienda" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-white/70">Inicio</Link>
                            <Link href="/articulos" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-white/70">Articulos</Link>
                            <Link href="/contacto" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-white/70">Contacto</Link>
                        </div>
                        <Link href="/cart">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </Link>
                    </nav>
                </header>
      <h2 class="pt-8 pb-4">Tu Carrito</h2>

      <div v-if="cartItems.length > 0">
        <div v-for="item in cartItems" :key="item.id">
          <p>
            <div class="flex-shrink-0 p-1">
                <img class="w-24 h-36 object-cover rounded" :src="`${item.articulo.imagen}`" alt="Imagen Articulo">
            </div>
            <div class="ml-4">
            <h2 class="text-lg font-semibold mb-2">{{ item.articulo.nombre }}</h2>
            <p class="text-blue-700 font-bold">{{ item.cantidad }}</p>
            <p class="text-blue-700 font-semibold">{{ item.articulo.precio }} €</p>
            </div>
            <Link :href="`/cart/${item.articulo.id}`" method="DELETE" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-2  mx-1 inline-block">Eliminar</Link>
          </p>
        </div>

        <p>Total: {{ precioTotal }} €</p>
      </div>

      <p v-else>Tu carrito está vacío.</p>
    </div>
</div>
  </template>
