<script setup>

import { Link, router, Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import skateSection from '../../../images/skateSection.webp'
import clothesSection from '../../../images/clothesSection.webp'

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

// Función para determinar si la URL es completa (comienza con http:// o https://)
const isFullUrl = (url) => {
    return url && (url.startsWith('http://') || url.startsWith('https://'));
};

const props = defineProps({
    articulos: Object,
    categorias: Array,
});

const addToCart = (articuloId) => {
    router.post('/cart/' + articuloId, { cantidad: 1 });
};

// Añadir información para promociones
const promociones = ref([
    { id: 1, titulo: 'REBAJAS DE PRIMAVERA', descripcion: 'Hasta 40% de descuento en ropa de temporada', color: 'bg-pink-600' },
    { id: 2, titulo: 'ENVÍO GRATIS', descripcion: 'En pedidos superiores a 50€', color: 'bg-blue-600' },
]);

const promocionActual = ref(0);

// Cambiar promoción automáticamente
onMounted(() => {
    // Rotación de promociones
    setInterval(() => {
        promocionActual.value = (promocionActual.value + 1) % promociones.value.length;
    }, 5000);
});

</script>

<template>
    <Head title="Novedades" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white min-h-screen">
        <div class="flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                <header class="min-w-full">
                    <nav class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-black text-white dark:text-white/80 border-b border-white/20">
                            <div class="space-x-4">
                                <Link href="/" class="text-sm font-medium hover:text-black dark:hover:text-white/50">Inicio</Link>

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
                                     class="absolute z-10 left-0 mt-2 w-56 origin-top-left bg-white dark:bg-pink-800/95 rounded-md shadow-lg ring-0 ring-black ring-opacity-5 focus:outline-none">
                                    <!-- Botón para cerrar en dispositivos móviles -->
                                    <button v-if="isMobile"
                                            @click="hideMenu"
                                            class="absolute top-1 right-2 text-gray-500 hover:text-gray-700">
                                        ✕
                                    </button>

                                    <div class="py-1" role="none">
                                        <!-- Para móvil usamos la clase Link con un div y @click para cerrar el menú -->
                                        <template v-if="isMobile">
                                            <div @click="hideMenu" class="text-gray-700 dark:text-white">
                                                <Link href="/articulos" class="block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-black/15">
                                                    Todas las categorías
                                                </Link>
                                            </div>
                                        </template>
                                        <template v-else>
                                            <Link href="/articulos" class="text-gray-700 dark:text-white block px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-black/15">
                                                Todas las categorías
                                            </Link>
                                        </template>

                                        <div v-for="categoria in categorias" :key="categoria.id" class="text-gray-800 dark:text-white">
                                            <!-- Categoría padre -->
                                            <template v-if="isMobile">
                                                <div @click="hideMenu">
                                                    <Link :href="`/articulos?categoria=${categoria.id}`"
                                                         class="block px-4 py-2 text-sm font-bold hover:bg-gray-100 dark:hover:bg-black/15">
                                                        {{ categoria.nombre }}
                                                    </Link>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <Link :href="`/articulos?categoria=${categoria.id}`"
                                                     class="block px-4 py-2 text-sm font-bold hover:bg-gray-100 dark:hover:bg-black/15">
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
                                                             class="block px-8 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-black/15">
                                                            {{ subcategoria.nombre }}
                                                        </Link>
                                                    </div>
                                                </template>
                                                <template v-else>
                                                    <Link v-for="subcategoria in categoria.subcategorias"
                                                         :key="subcategoria.id"
                                                         :href="`/articulos?categoria=${subcategoria.id}`"
                                                         class="block px-8 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-black/15">
                                                        {{ subcategoria.nombre }}
                                                    </Link>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

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
                        <Link v-if="$page.props.auth.user && $page.props.auth.user.rol == 'admin'" href="/dashboard"
                            class="text-sm font-medium  hover:text-black dark:hover:text-white/50">Dashboard</Link>
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

                <main class="w-full">
                    <!-- Banner promocional -->
                    <div v-if="promociones.length > 0"
                         :class="[promociones[promocionActual].color, 'text-white py-3 text-center transition-all duration-500 ease-in-out']">
                        <div class="container mx-auto">
                            <h3 class="text-xl font-bold">{{ promociones[promocionActual].titulo }}</h3>
                            <p>{{ promociones[promocionActual].descripcion }}</p>
                        </div>
                    </div>

                    <!-- Hero section mejorado -->
                    <div class="bg-gradient-to-b from-black/90 to-gray-900 text-white py-16 px-4">
                        <div class="container mx-auto text-center max-w-4xl">
                            <h1 v-if="$page.props.auth.user" class="text-5xl font-bold mb-4">
                                ¡Bienvenido, {{ $page.props.auth.user.name }}!
                            </h1>
                            <template v-else>
                                <h1 class="text-5xl text-pink-600 mb-4 font-serif">Bienvenido a Caveman!</h1>
                            </template>
                            <p class="text-xl mb-8">Tu tienda de moda skater y streetwear</p>
                            <div class="flex justify-center gap-4">
                                <Link href="/articulos" class="bg-white text-black font-bold py-3 px-6 rounded-full hover:bg-opacity-90 transition">
                                    Ver todos los productos
                                </Link>
                                <Link v-if="!$page.props.auth.user" href="/register" class="bg-transparent border-2 border-white py-3 px-6 rounded-full hover:bg-white/10 transition">
                                    Crear cuenta
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Categorías principales - Responsive -->
                    <div class="container mx-auto px-4 py-12">
                        <h2 class="text-3xl font-bold text-center mb-8 dark:text-white">Categorías Principales</h2>
                        <div class="flex flex-col md:flex-row gap-6 justify-center">
                            <Link :href="`/articulos?categoria=1`" class="relative group overflow-hidden rounded-lg w-full md:w-1/2">
                                <img :src="clothesSection" alt="Clothes Section" class="w-full h-[400px] object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center transition-all duration-300">
                                    <h2 class="text-white text-4xl font-bold">Ropa</h2>
                                </div>
                                <div class="absolute inset-0 bg-white/15 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </Link>
                            <Link :href="`/articulos?categoria=5`" class="relative group overflow-hidden rounded-lg w-full md:w-1/2">
                                <img :src="skateSection" alt="Skate Section" class="w-full h-[400px] object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center transition-all duration-300">
                                    <h2 class="text-white text-4xl font-bold">Skate</h2>
                                </div>
                                <div class="absolute inset-0 bg-white/15 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </Link>
                        </div>
                    </div>

                    <!-- Productos destacados -->
                    <div class="bg-gray-100 dark:bg-gray-900 py-12 px-4">
                        <div class="container mx-auto">
                            <h2 class="text-3xl font-bold text-center mb-8 dark:text-white">Productos Destacados</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div v-for="articulo in articulos" :key="articulo.id"
                                     class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden transition-transform hover:scale-105">
                                    <img v-if="articulo.imagenes && articulo.imagenes.length > 0"
                                         :src="articulo.imagenes[0].ruta"
                                         :alt="articulo.nombre"
                                         class="w-full h-48 object-cover">
                                    <div v-else class="w-full h-48 bg-gray-300 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-gray-500 dark:text-gray-400">Sin imagen</span>
                                    </div>
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold dark:text-white">{{ articulo.nombre }}</h3>
                                        <p class="text-gray-600 dark:text-gray-300">{{ articulo.precio.toFixed(2) }} €</p>
                                        <div class="mt-4 flex justify-between">
                                            <Link :href="`/articulos/${articulo.id}`" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                                Ver detalles
                                            </Link>
                                            <div class="flex items-center space-x-2">
    <!-- Botones de administración solo visibles para administradores -->
    <template v-if="$page.props.auth.user && $page.props.auth.user.rol === 'admin'">
        <Link :href="`/articulos/${articulo.id}/edit`"
              class="flex items-center justify-center h-8 w-8 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>
        </Link>
        <Link :href="`/articulos/${articulo.id}`"
              method="delete"
              as="button"
              class="flex items-center justify-center h-8 w-8 bg-red-600 text-white rounded-md hover:bg-red-700 transition"
              onclick="return confirm('¿Estás seguro de que deseas eliminar este artículo?')">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>
        </Link>
    </template>
    <button @click="addToCart(articulo.id)"
            class="flex items-center justify-center h-8 px-3 bg-black text-white dark:bg-white dark:text-black rounded-md hover:bg-gray-800 dark:hover:bg-gray-200 transition">
        Añadir
    </button>
</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>

                <!-- Espacio para implementar un footer personalizado -->
        </div>
    </div>
</template>
