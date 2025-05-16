<script setup>
import { Link, router } from '@inertiajs/vue3';
import Logo from '../../images/logo.png';
import { ref, onMounted } from 'vue';

// Lógica para cerrar sesión
const logout = () => {
    router.post(route('logout'));
};

// Variables para el menú de categorías
const showCategories = ref(false);
const isMobile = ref(false);

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

onMounted(() => {
    // Detectar si es dispositivo móvil al cargar
    checkIfMobile();
    // Agregar listener para cambios de tamaño de ventana
    window.addEventListener('resize', checkIfMobile);
});

const checkIfMobile = () => {
    isMobile.value = window.innerWidth < 768; // 768px es un breakpoint común para tabletas/móviles
};

// Props para recibir las categorías desde el componente padre
const props = defineProps({
    categorias: {
        type: Array,
        default: () => []
    },
    categoriaSeleccionada: {
        type: String,
        default: null
    }
});

// El conteo de elementos del carrito viene ahora compartido desde Inertia en todas las páginas
// No necesitamos recibirlo como prop, podemos accederlo directamente desde $page.props
</script>

<template>
    <header class="min-w-full sticky top-0 z-50">
        <nav
            class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-white/95 dark:bg-black/95 backdrop-blur-sm text-gray-800 dark:text-white/80 border-b border-gray-200 dark:border-white/20 shadow-lg">
            <div class="flex items-center space-x-4">
                <Link href="/" class="flex items-center hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                    <img :src="Logo" alt="Logo" class="h-14 w-auto" style="filter: invert(25%) sepia(64%) saturate(4215%) hue-rotate(269deg) brightness(91%) contrast(93%);">
                </Link>

                <!-- Menú desplegable de categorías - Adaptado para móvil y escritorio -->
                <div class="relative inline-block text-left">
                    <!-- En móvil: toggle al hacer clic, en escritorio: hover -->
                    <div v-if="isMobile">
                        <button @click="toggleMenu"
                                class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                            Articulos
                        </button>
                    </div>
                    <div v-else>
                        <Link href="/articulos"
                             @mouseenter="showMenu"
                             class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                            Articulos
                        </Link>
                    </div>

                    <!-- Menú desplegable - visible al hover en escritorio o al hacer clic en móvil -->
                    <div v-show="showCategories"
                         @mouseenter="!isMobile && showMenu()"
                         @mouseleave="!isMobile && hideMenu()"
                         class="absolute z-10 left-0 mt-2 w-56 origin-top-left bg-white dark:bg-gray-800/95 rounded-md shadow-lg ring-1 ring-gray-200 dark:ring-black/50 ring-opacity-5 focus:outline-none">
                        <!-- Botón para cerrar en dispositivos móviles -->
                        <button v-if="isMobile"
                                @click="hideMenu"
                                class="absolute top-1 right-2 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
                            ✕
                        </button>

                        <div class="py-1" role="none">
                            <!-- Para móvil usamos la clase Link con el método @click -->
                            <template v-if="isMobile">
                                <div @click="hideMenu" class="text-gray-700 dark:text-white">
                                    <Link href="/articulos"
                                         :class="['block px-4 py-2 text-sm hover:bg-gray-100 hover:text-purple-700 dark:hover:bg-gray-700 dark:hover:text-white',
                                                 !categoriaSeleccionada ? 'font-bold bg-purple-50 text-purple-700 dark:bg-gray-700 dark:text-white' : '']">
                                        Todas las categorías
                                    </Link>
                                </div>
                            </template>
                            <template v-else>
                                <Link href="/articulos"
                                     :class="['text-gray-700 dark:text-white block px-4 py-2 text-sm hover:bg-gray-100 hover:text-purple-700 dark:hover:bg-gray-700 dark:hover:text-white',
                                             !categoriaSeleccionada ? 'font-bold bg-purple-50 text-purple-700 dark:bg-gray-700 dark:text-white' : '']">
                                    Todas las categorías
                                </Link>
                            </template>

                            <div v-for="categoria in props.categorias" :key="categoria.id" class="text-gray-800 dark:text-white">
                                <!-- Categoría padre -->
                                <template v-if="isMobile">
                                    <div @click="hideMenu">
                                        <Link :href="`/articulos?categoria=${categoria.id}`"
                                             :class="['block px-4 py-2 text-sm font-bold hover:bg-gray-100 hover:text-purple-700 dark:hover:bg-gray-700 dark:hover:text-white',
                                                     categoriaSeleccionada == categoria.id ? 'bg-purple-50 text-purple-700 dark:bg-gray-700 dark:text-white' : '']">
                                            {{ categoria.nombre }}
                                        </Link>
                                    </div>
                                </template>
                                <template v-else>
                                    <Link :href="`/articulos?categoria=${categoria.id}`"
                                         :class="['block px-4 py-2 text-sm font-bold hover:bg-gray-100 hover:text-purple-700 dark:hover:bg-gray-700 dark:hover:text-white',
                                                 categoriaSeleccionada == categoria.id ? 'bg-purple-50 text-purple-700 dark:bg-gray-700 dark:text-white' : '']">
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
                                                 :class="['block px-8 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 hover:text-purple-700 dark:hover:bg-gray-700 dark:hover:text-white',
                                                         categoriaSeleccionada == subcategoria.id ? 'bg-purple-50 text-purple-700 dark:bg-gray-700 dark:text-white font-semibold' : '']">
                                                {{ subcategoria.nombre }}
                                            </Link>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <Link v-for="subcategoria in categoria.subcategorias"
                                             :key="subcategoria.id"
                                             :href="`/articulos?categoria=${subcategoria.id}`"
                                             :class="['block px-8 py-1 text-sm text-gray-600 dark:text-gray-300 hover:bg-gray-100 hover:text-purple-700 dark:hover:bg-gray-700 dark:hover:text-white',
                                                     categoriaSeleccionada == subcategoria.id ? 'bg-purple-50 text-purple-700 dark:bg-gray-700 dark:text-white font-semibold' : '']">
                                            {{ subcategoria.nombre }}
                                        </Link>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <Link v-if="$page.props.auth.user" href="/pedidos" class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                Pedidos</Link>
                <Link v-if="$page.props.auth.user" href="/perfil" class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">{{
                    $page.props.auth.user.name }}</Link>
                <template v-else>
                    <Link href="/login" class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                        Log in
                    </Link>
                    <Link href="/register" class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                        Register
                    </Link>
                </template>
                <Link v-if="$page.props.auth.user && $page.props.auth.user.rol == 'admin'" href="/dashboard" class="text-sm font-medium hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                    Dashboard
                </Link>
            </div>
            <div class="flex items-center gap-3">
                <form v-if="$page.props.auth.user" @submit.prevent="logout">
                    <button type="submit"
                        class="text-sm font-medium px-4 text-gray-700 dark:text-white/80 hover:text-purple-600 dark:hover:text-purple-400 transition-colors">
                        Log Out
                    </button>
                </form>
                <Link v-if="$page.props.auth.user" href="/cart" class="relative">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="cart-icon size-6 stroke-gray-700 hover:stroke-purple-600 dark:stroke-white dark:hover:stroke-purple-400 transition-colors">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    <span v-if="$page.props.cartItemsCount > 0" class="absolute -top-2 -right-2 bg-purple-600 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                        {{ $page.props.cartItemsCount }}
                    </span>
                </Link>
            </div>
        </nav>
    </header>
</template>
