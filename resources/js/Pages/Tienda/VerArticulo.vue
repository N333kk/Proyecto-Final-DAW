<script setup>
import { Link, router } from '@inertiajs/vue3'
import 'vue3-carousel/dist/carousel.css';
import { Carousel, Slide, Pagination, Navigation } from 'vue3-carousel';
import { ref, computed } from 'vue';

const logout = () => {
    router.post(route('logout'));
};

const props = defineProps({
    articulo: Object,
    esFavorito: {
        type: Boolean,
        default: false
    }
});

// Usamos un ref para poder cambiar su estado reactivamente cuando el usuario hace clic
const esFavoritoActual = ref(props.esFavorito);

const toggleFavorito = () => {
    // Actualización optimista: cambiamos el estado visual inmediatamente
    esFavoritoActual.value = !esFavoritoActual.value;

    // Guardamos el estado anterior por si hay errores
    const estadoAnterior = esFavoritoActual.value;

    // Enviamos la petición al servidor
    router.post(route('favoritos.toggle', { id: props.articulo.id }), {}, {
        preserveScroll: true,
        onError: () => {
            // En caso de error, revertimos el cambio
            esFavoritoActual.value = !estadoAnterior;
        }
    });
};

const isFullUrl = (url) => {
    return url && (url.startsWith('http://') || url.startsWith('https://'));
};

const imagenes = computed(() => {
    if (props.articulo.imagenes && props.articulo.imagenes.length > 0) {
        return props.articulo.imagenes.map(img => ({
            src: isFullUrl(img.ruta) ? img.ruta : `/storage/${img.ruta}`,
            alt: `Imagen de ${props.articulo.nombre}`
        }));
    } else {
        return [{
            src: '/img/placeholder.webp',
            alt: 'Imagen no disponible'
        }];
    }
});

const responsiveOptions = ref([
    {
        breakpoint: '1024px',
        numVisible: 5
    },
    {
        breakpoint: '768px',
        numVisible: 3
    },
    {
        breakpoint: '560px',
        numVisible: 1
    }
]);

</script>

<template>
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white min-h-screen">
        <div class="flex flex-col selection:bg-[#FF2D20] selection:text-white">
            <header class="min-w-full">
                <nav
                    class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-black text-white dark:text-white/80 border-b border-white/20">
                    <!-- Navegación sin cambios -->
                    <div class="space-x-4">
                        <Link href="/" class="text-sm font-medium hover:text-black dark:hover:text-white/50">
                        Inicio</Link>
                        <Link href="/articulos" class="text-sm font-medium hover:text-black dark:hover:text-white/50">
                        Articulos</Link>
                        <Link v-if="$page.props.auth.user" href="/pedidos"
                            class="text-sm font-medium hover:text-black dark:hover:text-white/50">Pedidos</Link>
                        <Link v-if="$page.props.auth.user" href="/perfil"
                            class="text-sm font-medium hover:text-black dark:hover:text-white/50">{{
                                $page.props.auth.user.name }}</Link>
                        <template v-else>
                            <Link href="/login" class="text-sm font-medium hover:text-black dark:hover:text-white/50">
                            Log in
                            </Link>
                            <Link href="/register"
                                class="text-sm font-medium hover:text-black dark:hover:text-white/50">
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

            <main class="w-full p-4 mt-6 ">
                <div class="flex flex-col md:flex-row gap-8">
                    <div class="w-full md:w-4/5">
                        <Carousel
                            class="w-full md:border-2 md:border-white/10 sm:border-black border-black rounded-lg p-4"
                            ref="carousel"
                            :items-to-show="1"
                            :wrap-around="true"
                        >
                            <Slide v-for="(imagen, index) in imagenes" :key="index">
                                <img
                                    :src="imagen.src"
                                    :alt="imagen.alt"
                                    class="w-full h-[500px] object-contain"
                                />
                            </Slide>
                            <template #addons>
                                <Navigation />
                                <Pagination />
                            </template>
                        </Carousel>

                        <div class="flex mt-4 overflow-x-auto space-x-2 p-2">
                            <img
                                v-for="(imagen, index) in imagenes"
                                :key="index"
                                :src="imagen.src"
                                :alt="imagen.alt"
                                class="w-16 h-16 object-cover cursor-pointer rounded"
                                @click="$refs.carousel.slideTo(index)"
                            />
                        </div>
                    </div>

                    <div class="w-full md:w-1/5 flex flex-col">

                        <div class="mb-8">
                            <h1 class="text-3xl md:text-4xl font-bold text-white dark:text-white/80">
                                {{ articulo.nombre }}
                            </h1>
                            <p class="text-sm text-white/70 dark:text-white/50 font-medium mt-2">
                                {{ articulo.categoria && articulo.categoria.length > 0
     ? articulo.categoria[0].nombre
     : 'Sin categoría' }}
                            </p>
                        </div>

                        <div class="mb-8">
                            <h2 class="text-xl font-semibold text-white/90 dark:text-white/70 mb-2">
                                Descripción del producto
                            </h2>
                            <p class="text-white/80 dark:text-white/60">
                                {{ articulo.descripcion }}
                            </p>
                        </div>

                        <div class="mt-auto">
                            <p class="text-2xl font-bold text-white dark:text-white mb-2">
                                {{ articulo.precio }} €
                            </p>

                            <div class="flex space-x-2">
                                <Link
                                    :href="route('cart.store', { id: articulo.id })"
                                    method="post"
                                    as="button"
                                    type="button"
                                    class="flex-1 bg-white text-black font-semibold py-3 px-4 rounded-lg hover:bg-white/90 transition"
                                >
                                    Añadir al carrito
                                </Link>

                                <Link
                                    v-if="$page.props.auth.user"
                                    href="#"
                                    @click.prevent="toggleFavorito"
                                    class="bg-white/20 text-white font-semibold py-3 px-4 rounded-lg hover:bg-white/30 transition"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" :fill="esFavoritoActual ? 'currentColor' : 'none'"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                <!-- Footer content -->
            </footer>
        </div>
    </div>
</template>

<style>

</style>
