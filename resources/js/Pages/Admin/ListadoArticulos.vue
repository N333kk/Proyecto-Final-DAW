<script setup>
import { Head, Link, router } from '@inertiajs/vue3'

const logout = () => {
    router.post(route('logout'));
};


defineProps({
    articulos: Object,
});

const isFullUrl = (url) => {
    return url && (url.startsWith('http://') || url.startsWith('https://'));
};

</script>

<template>
<Head title="Articulos" />
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
                        <Link v-if="$page.props.auth.user && $page.props.auth.user.rol == 'admin'" href="/dashboard"
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

            <div class="">
                <div class="py-4 border-b w-full border-b-white/20 bg-black  flex justify-between items-center">
                    <h2 class="text-white py-2 px-4 mx-4 font-bold text-4xl">Listado de articulos</h2>
                    <Link v-if="$page.props.auth.user && $page.props.auth.user.rol === 'admin'" href="/articulos/create"
                        class="bg-gradient-to-br from-onyx-400 to-onyx-600 hover:from-onyx-500 hover:to-onyx-700 transition-all hover:px-8 text-white font-bold py-2 px-4 mx-4 rounded-full relative group">
                        <span class="block group-hover:hidden transition-all">+</span>
                        <p class="hidden group-hover:block transition-all">Nuevo Articulo</p>
                    </Link>
                </div>
                <ul class="p-8 grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">

                    <li class="bg-white p-6 rounded-lg shadow-md flex text-black" v-for="articulo in articulos"
                        :key="articulo.id">
                        <div class="flex-shrink-0 p-1">
                            <img class="w-32 h-32 object-cover rounded" :src="articulo.imagenes && articulo.imagenes.length > 0
              ? (isFullUrl(articulo.imagenes[0].ruta) ? articulo.imagenes[0].ruta : `/${articulo.imagenes[0].ruta}`)
              : '/img/placeholder.webp'"
                                alt="Imagen Articulo">
                        </div>
                        <div class="ml-4">
                            <h2 class="text-lg font-semibold mb-2">{{ articulo.nombre }}</h2>
                            <p class="text-black font-bold">{{ articulo.categoria && articulo.categoria.length > 0
     ? articulo.categoria[0].nombre
     : 'Sin categoría' }}</p>
                            <p class="text-black">{{ articulo.descripcion }}</p>
                            <p class="text-black font-semibold">{{ articulo.precio }} €</p>

                            <Link v-if="$page.props.auth.user && $page.props.auth.user.rol === 'admin'" :href="`/articulos/${articulo.id}/edit`"
                                class="bg-blue-700 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded mt-2 mx-1 inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
</svg>
</Link>
                            <Link v-if="$page.props.auth.user && $page.props.auth.user.rol === 'admin'" :href="`/articulos/${articulo.id}`"
                                method="DELETE"
                                class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-2  mx-1 inline-block">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
</svg>
</Link>
                            <Link :href="`/articulos/${articulo.id}`"
                                class="bg-gradient-to-r from-onyx-100 to-onyx-400 hover:from-onyx-400 hover:to-onyx-600  hover:text-onyx-100 text-white font-bold py-2 px-4 rounded mt-2 mx-1 inline-block">
                            Mostrar Articulo</Link>
                            <Link v-if="$page.props.auth.user" :href="`/cart/${articulo.id}`" method="POST"
                                class="bg-gradient-to-r from-onyx-100 to-onyx-400 hover:from-onyx-400 hover:to-onyx-600  hover:text-onyx-100 text-white font-bold py-2 px-4 rounded mt-2 mx-1 inline-block"
                                @click="addToCart(articulo.id)">Añadir al Carrito</Link>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>
