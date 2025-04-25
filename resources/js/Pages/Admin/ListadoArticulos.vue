<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import 'vue3-carousel/dist/carousel.css';
import { Carousel, Slide, Pagination, Navigation as CarouselNav } from 'vue3-carousel';
import { ref, onMounted } from 'vue'

const showCategories = ref(false);

// Función para mostrar el menú
const showMenu = () => {
    showCategories.value = true;
};

// Función para ocultar el menú
const hideMenu = () => {
    showCategories.value = false;
};

// Función para alternar el menú (para dispositivos táctiles)
const toggleMenu = () => {
    showCategories.value = !showCategories.value;
};

// Detectar si es dispositivo móvil
const isMobile = ref(false);

onMounted(() => {
    // Detectar si es dispositivo móvil al cargar
    checkIfMobile();
    // Agregar listener para cambios de tamaño de ventana
    window.addEventListener('resize', checkIfMobile);
});

const checkIfMobile = () => {
    isMobile.value = window.innerWidth < 768; // 768px es un breakpoint común para tabletas/móviles
};

const logout = () => {
    router.post(route('logout'));
};

const props = defineProps({
    articulos: Object,
    categorias: Array,
    categoriaSeleccionada: String,
    articulosFavoritos: {
        type: Array,
        default: () => []
    }
});

// Variable reactiva para manejar los favoritos (inicializada con los props)
const favoritosLocales = ref(props.articulosFavoritos || []);

const addToCart = (articuloId) => {
    router.post('/cart/' + articuloId, { cantidad: 1 });
};

const addToFavorites = (articuloId) => {
    // Actualización optimista
    const estaEnFavoritos = favoritosLocales.value.includes(articuloId);

    // Actualizamos el estado local inmediatamente
    if (estaEnFavoritos) {
        // Si está en favoritos, lo quitamos
        favoritosLocales.value = favoritosLocales.value.filter(id => id !== articuloId);
    } else {
        // Si no está en favoritos, lo añadimos
        favoritosLocales.value.push(articuloId);
    }

    // Enviamos la petición al servidor
    router.post('/favoritos/' + articuloId, {}, {
        preserveScroll: true,
        onError: () => {
            // En caso de error, revertimos el cambio
            if (estaEnFavoritos) {
                favoritosLocales.value.push(articuloId);
            } else {
                favoritosLocales.value = favoritosLocales.value.filter(id => id !== articuloId);
            }
        }
    });
};

// Función para eliminar artículo (solo para administradores)
const deleteArticulo = (articuloId) => {
    if (confirm('¿Estás seguro de que deseas eliminar este artículo? Esta acción no se puede deshacer.')) {
        router.delete(`/articulos/${articuloId}`, {
            preserveScroll: true,
            onSuccess: () => {
                // Mensaje opcional de éxito si está disponible en la aplicación
            }
        });
    }
};

// Función para manejar el clic en el artícul
</script>

<template>
<Head title="Articulos" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white  min-h-screen">
        <div class="flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <header class="min-w-full">
                <nav
                    class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-black text-white dark:text-white/80 border-b dark:border-white/20 border-black/20">
                    <div class="space-x-4">
                        <Link href="/" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">
                        Inicio</Link>

                        <!-- Menú desplegable de categorías - Adaptado para móvil y escritorio -->
                        <div class="relative inline-block text-left">
                            <!-- En móvil: toggle al hacer clic, en escritorio: hover -->
                            <div v-if="isMobile">
                                <button @click="toggleMenu"
                                        class="text-sm font-medium hover:text-black dark:hover:text-white/50">
                                    Articulos
                                </button>
                            </div>
                            <div v-else>
                                <Link href="/articulos"
                                     @mouseenter="showMenu"
                                     class="text-sm font-medium hover:text-black dark:hover:text-white/50">
                                    Articulos
                                </Link>
                            </div>

                            <!-- Menú desplegable - visible al hover en escritorio o al hacer clic en móvil -->
                            <div v-show="showCategories"
                                 @mouseenter="!isMobile && showMenu()"
                                 @mouseleave="!isMobile && hideMenu()"
                                 class="absolute z-10 left-0 mt-2 w-56 origin-top-left bg-white dark:bg-gray-800 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none">
                                <!-- Botón para cerrar en dispositivos móviles -->
                                <button v-if="isMobile"
                                        @click="hideMenu"
                                        class="absolute top-1 right-2 text-gray-500 hover:text-gray-700">
                                    ✕
                                </button>

                                <div class="py-1" role="none">
                                    <!-- Para móvil usamos la clase Link con el método @click -->
                                    <template v-if="isMobile">
                                        <div @click="hideMenu" class="text-gray-700 dark:text-white">
                                            <Link href="/articulos"
                                                 :class="['block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700',
                                                         !categoriaSeleccionada ? 'font-bold bg-gray-100 dark:bg-gray-700' : '']">
                                                Todas las categorías
                                            </Link>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <Link href="/articulos"
                                             :class="['text-gray-700 dark:text-white block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700',
                                                     !categoriaSeleccionada ? 'font-bold bg-gray-100 dark:bg-gray-700' : '']">
                                            Todas las categorías
                                        </Link>
                                    </template>

                                    <div v-for="categoria in categorias" :key="categoria.id" class="text-gray-800 dark:text-white">
                                        <!-- Categoría padre -->
                                        <template v-if="isMobile">
                                            <div @click="hideMenu">
                                                <Link :href="`/articulos?categoria=${categoria.id}`"
                                                     :class="['block px-4 py-2 text-sm font-bold hover:bg-gray-100 dark:hover:bg-gray-700',
                                                             categoriaSeleccionada == categoria.id ? 'bg-gray-100 dark:bg-gray-700' : '']">
                                                    {{ categoria.nombre }}
                                                </Link>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <Link :href="`/articulos?categoria=${categoria.id}`"
                                                 :class="['block px-4 py-2 text-sm font-bold hover:bg-gray-100 dark:hover:bg-gray-700',
                                                         categoriaSeleccionada == categoria.id ? 'bg-gray-100 dark:bg-gray-700' : '']">
                                                {{ categoria.nombre }}
                                            </Link>
                                        </template>

                                        <!-- Subcategorías -->
                                        <div v-if="categoria.subcategorias && categoria.subcategorias.length > 0">
                                            <template v-if="isMobile">
                                                <div v-for="subcategoria in categoria.subcategorias"
                                                     :key="subcategoria.id"
                                                     @click="hideMenu">
                                                    <Link :href="`/articulos?categoria=${subcategoria.id}`"
                                                         :class="['block px-8 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
                                                                 categoriaSeleccionada == subcategoria.id ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : '']">
                                                        {{ subcategoria.nombre }}
                                                    </Link>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <Link v-for="subcategoria in categoria.subcategorias"
                                                     :key="subcategoria.id"
                                                     :href="`/articulos?categoria=${subcategoria.id}`"
                                                     :class="['block px-8 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700',
                                                             categoriaSeleccionada == subcategoria.id ? 'bg-gray-100 dark:bg-gray-700 font-semibold' : '']">
                                                    {{ subcategoria.nombre }}
                                                </Link>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                    <h2 class="text-white py-2 px-4 mx-4 font-bold text-4xl">
                        Listado de artículos
                        <span v-if="categoriaSeleccionada" class="text-xl text-gray-300">
                            (Filtrado por categoría)
                        </span>
                    </h2>
                    <Link v-if="$page.props.auth.user && $page.props.auth.user.rol === 'admin'" href="/articulos/create"
                        class="bg-gradient-to-br from-onyx-400 to-onyx-600 hover:from-onyx-500 hover:to-onyx-700 transition-all hover:px-8 text-white font-bold py-2 px-4 mx-4 rounded-full relative group">
                        <span class="block group-hover:hidden transition-all">+</span>
                        <p class="hidden group-hover:block transition-all">Nuevo Articulo</p>
                    </Link>
                </div>
                <!-- Grid responsivo con distribución específica de columnas -->
                <div class="p-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-1 sm:gap-3 md:gap-4">
                    <!-- Tarjetas con tamaño dinámico que se adaptan al contenedor -->
                    <div class="rounded-lg transition-all w-full relative group overflow-hidden"
                         v-for="articulo in articulos"
                         :key="articulo.id">
                        <!-- Carousel sin Link para evitar la redirección -->
                        <Carousel
                            class="w-full rounded-lg carousel-custom"
                            ref="carousel"
                            :items-to-show="1"
                            :wrap-around="true"
                        >
                            <Slide v-for="(imagen, index) in articulo.imagenes" :key="index">
                                <div class="aspect-square w-full">
                                    <img
                                        :src="imagen.ruta"
                                        :alt="imagen.alt"
                                        class="w-full h-full object-contain p-1"
                                    />
                                </div>
                            </Slide>
                            <Slide v-if="articulo.imagenes.length === 0">
                                <div class="aspect-square w-full">
                                    <img
                                        src="/img/placeholder.webp"
                                        alt="Imagen no disponible"
                                        class="w-full h-full object-contain p-1"
                                    />
                                </div>
                            </Slide>
                            <template #addons>
                                <CarouselNav />
                            </template>
                        </Carousel>

                        <!-- Overlay con botones (visible al hacer hover) - Con mayor transparencia -->
                        <div class="absolute inset-0 bg-black/10 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2 z-10 pointer-events-none">
                            <!-- Contenedor de botones en la parte inferior -->
                            <div class="w-full pointer-events-auto">
                                <!-- Si el usuario NO está logueado: solo muestra el botón Ver artículo al 100% de ancho -->
                                <div v-if="!$page.props.auth.user" class="w-full">
                                    <Link :href="`/articulos/${articulo.id}`"
                                        class="w-full bg-white/90 hover:bg-white text-black font-semibold py-2 px-4 rounded-md transition-colors flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                        Ver artículo
                                    </Link>
                                </div>

                                <!-- Si el usuario SÍ está logueado: muestra los 3 botones en fila -->
                                <div v-else class="w-full flex justify-between gap-1">
                                    <!-- Botón para ver artículo (1/3 del ancho) -->
                                    <Link :href="`/articulos/${articulo.id}`"
                                        class="flex-1 bg-white/90 hover:bg-white text-black font-semibold py-2 px-2 rounded-md transition-colors flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </Link>

                                    <!-- Botones de administración solo visibles para administradores -->
                                    <template v-if="$page.props.auth.user && $page.props.auth.user.rol === 'admin'">
                                        <!-- Botón para editar artículo -->
                                        <Link :href="`/articulos/${articulo.id}/edit`"
                                            class="flex-1 bg-blue-600/90 hover:bg-blue-600 text-white font-semibold py-2 px-2 rounded-md transition-colors flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </Link>

                                        <!-- Botón para eliminar artículo -->
                                        <Link :href="`/articulos/${articulo.id}`" method="delete" as="button"
                                            class="flex-1 bg-red-600/90 hover:bg-red-600 text-white font-semibold py-2 px-2 rounded-md transition-colors flex items-center justify-center"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar este artículo? Esta acción no se puede deshacer.')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </Link>
                                    </template>

                                    <!-- Botón para añadir al carrito (1/3 del ancho) -->
                                    <button @click="addToCart(articulo.id)"
                                        class="flex-1 bg-blue-500/90 hover:bg-blue-500 text-white font-semibold py-2 px-2 rounded-md transition-colors flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                    </button>

                                    <!-- Botón para añadir a favoritos (1/3 del ancho) -->
                                    <button @click="addToFavorites(articulo.id)"
                                        class="flex-1 bg-red-500/90 hover:bg-red-500 text-white font-semibold py-2 px-2 rounded-md transition-colors flex items-center justify-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" :fill="favoritosLocales.includes(articulo.id) ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style>
/* Estilos personalizados para los botones de navegación del carrusel */
.carousel-custom {
    position: relative;
    overflow: hidden; /* Asegura que los elementos no se salgan del contenedor */
}

.carousel-custom .carousel__prev,
.carousel-custom .carousel__next {
    background-color: rgba(128, 128, 128, 0.7) !important; /* Gris semitransparente */
    border-radius: 50%;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
    transition: all 0.3s ease;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 5;
}

.carousel-custom .carousel__prev {
    left: 5px; /* Posición desde el borde izquierdo */
}

.carousel-custom .carousel__next {
    right: 5px; /* Posición desde el borde derecho */
}

.carousel-custom .carousel__prev:hover,
.carousel-custom .carousel__next:hover {
    background-color: rgba(128, 128, 128, 0.9) !important; /* Gris más oscuro al hover */
    transform: translateY(-50%) scale(1.1);
}

/* Aseguramos que los iconos dentro de los botones sean blancos */
.carousel-custom .carousel__prev i,
.carousel-custom .carousel__next i {
    color: white !important;
}
</style>
