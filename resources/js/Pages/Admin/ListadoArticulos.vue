<script setup>
import { Link, router } from '@inertiajs/vue3'

const logout = () => {
    router.post(route('logout'));
};


defineProps({
    articulos: Object,
});

</script>

<template>

    <div class=" bg-zinc-900 text-whitel">
        <header class="min-w-full pb-4">
                    <nav class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-neutral-600">
                            <div class="space-x-4">
                                <Link href="/tienda" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-black">Inicio</Link>
                            <Link href="/articulos" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-black">Articulos</Link>
                            <Link href="/pedidos" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-black">Pedidos</Link>
                            <Link href="/perfil" class="text-sm font-medium text-black dark:text-white/70 hover:text-black dark:hover:text-black">{{ $page.props.auth.user.name }}</Link>
                        </div>
                        <div class="flex">
                            <form @submit.prevent="logout">
                                            <button type="submit" class="text-sm font-medium px-4 text-black dark:text-white/70 hover:text-black dark:hover:text-black">
                                                Log Out
                                            </button>
                                        </form>
                        <Link href="/cart">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 dark:stroke-white/70 dark:hover:stroke-black">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </Link>
                    </div>
                    </nav>
                </header>

                <div class="p-8">
                    <div class="p-4 border border-zinc-700 rounded-xl bg-neutral-600 flex justify-between items-center">
                        <h2 class="text-zinc-900 py-2 px-4">Listado de articulos</h2>
        <Link v-if="$page.props.auth.user.rol === 'admin'" href="/articulos/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">Crear Articulo</Link>
                    </div>
    <ul class="p-8 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">

        <li class="bg-white p-6 rounded-lg shadow-md flex text-black" v-for="articulo in articulos" :key="articulo.id">
            <div class="flex-shrink-0 p-1">
                <img class="w-24 h-36 object-cover rounded" :src="`${articulo.imagen}`" alt="Imagen Articulo">
            </div>
            <div class="ml-4">
            <h2 class="text-lg font-semibold mb-2">{{ articulo.nombre }}</h2>
            <p class="text-blue-700 font-bold">{{ articulo.categoria }}</p>
            <p class="text-blue-700">{{ articulo.descripcion }}</p>
            <p class="text-blue-700 font-semibold">{{ articulo.precio }} €</p>

            <Link v-if="$page.props.auth.user.rol === 'admin'" :href="`/articulos/${articulo.id}/edit`" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mt-2 mx-1 inline-block">Editar</Link>
            <Link v-if="$page.props.auth.user.rol === 'admin'" :href="`/articulos/${articulo.id}`" method="DELETE" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-2  mx-1 inline-block">Eliminar</Link>
            <Link :href="`/articulos/${articulo.id}`" class="bg-zinc-800 hover:bg-zinc-500 text-white font-bold py-2 px-4 rounded mt-2 mx-1 inline-block">Mostrar Articulo</Link>
            <Link :href="`/cart/${articulo.id}`" method="POST" class="bg-zinc-800 hover:bg-zinc-500 text-white font-bold py-2 px-4 rounded mt-2 mx-1 inline-block" @click="addToCart(articulo.id)">Añadir al Carrito</Link>
        </div>
        </li>
    </ul>
</div>
    </div>

</template>
