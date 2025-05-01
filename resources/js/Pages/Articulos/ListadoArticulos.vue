<script setup>
import { Head, Link, router } from '@inertiajs/vue3'
import 'vue3-carousel/dist/carousel.css';
import { Carousel, Slide, Pagination, Navigation as CarouselNav } from 'vue3-carousel';
import { ref, onMounted } from 'vue';
import Navbar from '@/Components/Navbar.vue';

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

const calcularPrecioConDescuento = (precio, descuento) => {
    return (precio - (precio * (descuento / 100))).toFixed(2);
};
</script>

<template>
<Head title="Articulos" />
    <div class="bg-gray-50 text-gray-800 dark:bg-black dark:text-white min-h-screen">
        <div class="flex flex-col items-center justify-center selection:bg-purple-500 selection:text-white dark:selection:bg-[#FF2D20]">
            <Navbar />

            <div class="">
                <div class="py-4 border-b w-full border-b-gray-200 dark:border-b-white/20 bg-gradient-to-r from-purple-50 to-transparent dark:from-purple-900/10 dark:to-transparent flex justify-between items-center">
                    <h2 class="text-gray-800 dark:text-white py-2 px-8 font-extrabold text-4xl flex items-center">
                        Listado de artículos
                        <span v-if="categoriaSeleccionada" class="ml-3 text-xl font-normal text-gray-500 dark:text-gray-300">
                            (Filtrado por categoría)
                        </span>
                    </h2>
                    <Link v-if="$page.props.auth.user && $page.props.auth.user.rol === 'admin'" href="/articulos/create"
                        class="bg-gradient-to-br from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 dark:from-purple-600 dark:to-purple-700 dark:hover:from-purple-700 dark:hover:to-purple-800 transition-all hover:px-8 text-white font-bold py-2 px-4 mx-8 rounded-lg shadow-md">
                        <span class="block group-hover:hidden transition-all">+</span>
                        <p class="hidden group-hover:block transition-all">Nuevo Articulo</p>
                    </Link>
                </div>
                <!-- Grid responsivo con distribución específica de columnas -->
                <div class="p-4 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-5 md:gap-6">
                    <!-- Tarjetas con tamaño dinámico que se adaptan al contenedor -->
                    <div class="rounded-xl transition-all w-full relative group overflow-hidden shadow-sm hover:shadow-xl border border-gray-200 dark:border-gray-700/30 bg-white dark:bg-gray-800/40 hover:scale-[1.02] duration-300"
                         v-for="articulo in articulos"
                         :key="articulo.id">

                        <!-- Etiqueta con el precio -->
                        <div class="absolute top-2 right-2 z-20 bg-gradient-to-r from-purple-500 to-purple-600 dark:from-purple-600 dark:to-indigo-700 text-white py-1 px-3 rounded-full shadow-lg text-sm font-bold">
                            <div v-if="articulo.descuento && articulo.descuento > 0" class="flex flex-col items-end">
                                <span class="line-through text-white/70 text-xs">{{ articulo.precio }}€</span>
                                <span>{{ calcularPrecioConDescuento(articulo.precio, articulo.descuento) }}€</span>
                            </div>
                            <span v-else>{{ articulo.precio }}€</span>
                        </div>

                        <!-- Carousel sin Link para evitar la redirección -->
                        <Carousel
                            class="w-full rounded-t-xl carousel-custom bg-white dark:bg-gray-800"
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

                        <!-- Información del artículo -->
                        <div class="p-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1 line-clamp-1">{{ articulo.nombre }}</h3>

                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ articulo.categoria ? articulo.categoria.nombre : 'Sin categoría' }}</span>

                                <!-- Indicador de favorito (corazón) -->
                                <div v-if="$page.props.auth.user && favoritosLocales.includes(articulo.id)"
                                     class="text-pink-500 dark:text-pink-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5">
                                        <path d="M11.645 20.91l-.007-.003-.022-.012a15.247 15.247 0 01-5.201-3.893 10.225 10.225 0 01-2.56-6.625A5.868 5.868 0 019.033 5.25c1.292 0 2.526.503 3.467 1.404a6.625 6.625 0 013.467-1.404 5.868 5.868 0 015.18 5.127 10.225 10.225 0 01-2.56 6.625 15.247 15.247 0 01-5.2 3.893l-.023.012-.007.003h0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Overlay con botones (visible al hacer hover) - Con mayor transparencia -->
                        <div class="absolute inset-0 bg-gradient-to-t from-gray-800/60 via-transparent to-transparent dark:from-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-2 z-10 pointer-events-none">
                            <!-- Contenedor de botones en la parte inferior -->
                            <div class="w-full pointer-events-auto">
                                <!-- Si el usuario NO está logueado: solo muestra el botón Ver artículo al 100% de ancho -->
                                <div v-if="!$page.props.auth.user" class="w-full">
                                    <Link :href="`/articulos/${articulo.id}`"
                                        class="w-full bg-white hover:bg-gray-100 dark:bg-white/90 dark:hover:bg-white text-gray-800 dark:text-black font-semibold py-2 px-4 rounded-md transition-colors flex items-center justify-center gap-2 shadow-sm">
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
                                        class="flex-1 bg-white hover:bg-gray-100 dark:bg-white/90 dark:hover:bg-white text-gray-800 dark:text-black font-semibold py-2 px-2 rounded-md transition-colors flex items-center justify-center shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </Link>

                                    <!-- Botones de administración solo visibles para administradores -->
                                    <template v-if="$page.props.auth.user && $page.props.auth.user.rol === 'admin'">
                                        <!-- Botón para editar artículo -->
                                        <Link :href="`/articulos/${articulo.id}/edit`"
                                            class="flex-1 bg-blue-500 hover:bg-blue-600 dark:bg-blue-600/90 dark:hover:bg-blue-600 text-white font-semibold py-2 px-2 rounded-md transition-colors flex items-center justify-center shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </Link>

                                        <!-- Botón para eliminar artículo -->
                                        <Link :href="`/articulos/${articulo.id}`" method="delete" as="button"
                                            class="flex-1 bg-red-500 hover:bg-red-600 dark:bg-red-600/90 dark:hover:bg-red-600 text-white font-semibold py-2 px-2 rounded-md transition-colors flex items-center justify-center shadow-sm"
                                            onclick="return confirm('¿Estás seguro de que deseas eliminar este artículo? Esta acción no se puede deshacer.')">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </Link>
                                    </template>

                                    <!-- Botón para añadir al carrito (1/3 del ancho) -->
                                    <button @click="addToCart(articulo.id)"
                                        class="flex-1 bg-purple-500 hover:bg-purple-600 dark:bg-blue-500/90 dark:hover:bg-blue-500 text-white font-semibold py-2 px-2 rounded-md transition-colors flex items-center justify-center shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                    </button>

                                    <!-- Botón para añadir a favoritos (1/3 del ancho) -->
                                    <button @click="addToFavorites(articulo.id)"
                                        class="flex-1 bg-pink-500 hover:bg-pink-600 dark:bg-red-500/90 dark:hover:bg-red-500 text-white font-semibold py-2 px-2 rounded-md transition-colors flex items-center justify-center shadow-sm">
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
    background-color: rgba(139, 92, 246, 0.6) !important; /* Color púrpura semitransparente */
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white !important;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
    transition: all 0.3s ease;
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    z-index: 5;
    backdrop-filter: blur(3px);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.carousel-custom .carousel__prev {
    left: 8px; /* Posición desde el borde izquierdo */
}

.carousel-custom .carousel__next {
    right: 8px; /* Posición desde el borde derecho */
}

.carousel-custom .carousel__prev:hover,
.carousel-custom .carousel__next:hover {
    background-color: rgba(139, 92, 246, 0.9) !important; /* Púrpura más sólido al hover */
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.5);
}

/* Aseguramos que los iconos dentro de los botones sean blancos */
.carousel-custom .carousel__prev i,
.carousel-custom .carousel__next i {
    color: white !important;
    font-size: 1.2rem;
}

/* Efecto de escala al pasar el ratón sobre la imagen */
.carousel__slide img {
    transition: transform 0.3s ease;
}

.carousel__slide:hover img {
    transform: scale(1.03);
}
</style>
