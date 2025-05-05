<script setup>
import { Link, router, Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import skateSection from '../../../images/skateSection.webp'
import clothesSection from '../../../images/clothesSection.webp'
import Navbar from '@/Components/Navbar.vue'

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

// Variables para el modal de selección de talla
const showTallaModal = ref(false);
const currentArticulo = ref(null);
const selectedTallaId = ref(null);
const tallaError = ref('');

const addToCart = (articulo) => {
    // Guardar el artículo seleccionado
    currentArticulo.value = articulo;
    selectedTallaId.value = null;
    tallaError.value = '';

    // Si el artículo no tiene tallas, mostrar un error
    if (!articulo.tallas || articulo.tallas.length === 0) {
        alert('Este producto no tiene tallas disponibles');
        return;
    }

    // Si solo hay una talla disponible con stock, seleccionarla automáticamente
    const tallasConStock = articulo.tallas.filter(talla => talla.pivot.stock > 0);
    if (tallasConStock.length === 1) {
        selectedTallaId.value = tallasConStock[0].id;
        confirmarAddToCart();
        return;
    }

    // Mostrar el modal para seleccionar talla
    showTallaModal.value = true;
};

// Función para confirmar y añadir al carrito con la talla seleccionada
const confirmarAddToCart = () => {
    // Validar que se ha seleccionado una talla
    if (!selectedTallaId.value) {
        tallaError.value = 'Por favor, selecciona una talla';
        return;
    }

    // Enviar petición con la talla seleccionada
    router.post('/cart/' + currentArticulo.value.id, {
        talla_id: selectedTallaId.value
    }, {
        preserveScroll: true,
        onSuccess: () => {
            // Cerrar el modal y limpiar la selección
            showTallaModal.value = false;
            selectedTallaId.value = null;
            tallaError.value = '';
        },
        onError: (errors) => {
            tallaError.value = errors.talla_id || 'Ha ocurrido un error';
        }
    });
};

// Función para cerrar el modal
const closeTallaModal = () => {
    showTallaModal.value = false;
    selectedTallaId.value = null;
    tallaError.value = '';
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
    <div class="bg-gray-50 text-gray-800 dark:bg-black dark:text-white min-h-screen">
        <div class="flex flex-col items-center justify-center selection:bg-purple-500 selection:text-white dark:selection:bg-[#FF2D20]">
                <Navbar />

                <!-- Modal de selección de talla -->
                <div v-if="showTallaModal && currentArticulo"
                     class="fixed inset-0 flex items-center justify-center z-50 bg-black/50 backdrop-blur-sm">
                    <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-lg max-w-md w-full mx-4">
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">
                            Selecciona una talla
                        </h3>

                        <p class="text-gray-600 dark:text-gray-300 mb-4">
                            Para añadir "{{ currentArticulo.nombre }}" al carrito, necesitas seleccionar una talla.
                        </p>

                        <div class="flex flex-wrap gap-2 mb-4">
                            <button
                                v-for="talla in currentArticulo.tallas"
                                :key="talla.id"
                                @click="selectedTallaId = talla.id"
                                :disabled="talla.pivot.stock <= 0"
                                :class="[
                                    'px-4 py-2 border rounded-md transition-colors font-medium',
                                    selectedTallaId === talla.id ? 'bg-purple-600 text-white border-purple-600' : 'border-gray-300',
                                    talla.pivot.stock <= 0 ? 'opacity-50 cursor-not-allowed bg-gray-100 text-gray-500' : 'hover:border-purple-300'
                                ]"
                            >
                                {{ talla.talla }}
                                <span v-if="talla.pivot.stock <= 0" class="text-xs ml-1">(Agotada)</span>
                            </button>
                        </div>

                        <div v-if="tallaError" class="text-red-500 text-sm mb-4">
                            {{ tallaError }}
                        </div>

                        <div class="flex justify-end gap-2 mt-6">
                            <button
                                @click="closeTallaModal"
                                class="px-4 py-2 border border-gray-300 rounded-md hover:bg-gray-100 transition-colors text-gray-700 dark:border-gray-600 dark:hover:bg-gray-700 dark:text-white"
                            >
                                Cancelar
                            </button>
                            <button
                                @click="confirmarAddToCart"
                                class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-md transition-colors"
                            >
                                Añadir al carrito
                            </button>
                        </div>
                    </div>
                </div>

                <main class="w-full">
                    <!-- Banner promocional con colores actualizados -->
                    <div v-if="promociones.length > 0"
                         :class="[promociones[promocionActual].color === 'bg-pink-600' ? 'bg-purple-600 dark:bg-purple-800' : 'bg-blue-500 dark:bg-blue-700', 'text-white py-3 text-center transition-all duration-500 ease-in-out']">
                        <div class="container mx-auto">
                            <h3 class="text-xl font-bold">{{ promociones[promocionActual].titulo }}</h3>
                            <p>{{ promociones[promocionActual].descripcion }}</p>
                        </div>
                    </div>

                    <!-- Hero section mejorado con estilos adaptados al nuevo diseño -->
                    <div class="bg-gradient-to-b from-purple-50 via-white to-white dark:from-black/90 dark:via-gray-900 dark:to-gray-900 text-gray-800 dark:text-white py-16 px-4">
                        <div class="container mx-auto text-center max-w-4xl">
                            <h1 v-if="$page.props.auth.user" class="text-5xl font-bold mb-4 text-gray-800 dark:text-white">
                                ¡Bienvenido, {{ $page.props.auth.user.name }}!
                            </h1>
                            <template v-else>
                                <h1 class="text-5xl text-purple-600 dark:text-purple-500 mb-4 font-serif">Bienvenido a Caveman!</h1>
                            </template>
                            <p class="text-xl mb-8 text-gray-700 dark:text-gray-300">Tu tienda de moda skater y streetwear</p>
                            <div class="flex justify-center gap-4">
                                <Link href="/articulos" class="bg-purple-600 hover:bg-purple-700 text-white font-bold py-3 px-6 rounded-lg hover:shadow-md transition-all duration-300">
                                    Ver todos los productos
                                </Link>
                                <Link v-if="!$page.props.auth.user" href="/register" class="bg-white text-purple-600 border-2 border-purple-600 hover:bg-purple-50 dark:bg-transparent dark:text-white dark:border-white dark:hover:bg-white/10 py-3 px-6 rounded-lg hover:shadow-md transition-all duration-300">
                                    Crear cuenta
                                </Link>
                            </div>
                        </div>
                    </div>

                    <!-- Categorías principales con estilos mejorados -->
                    <div class="container mx-auto px-4 py-12">
                        <h2 class="text-3xl font-bold text-center mb-8 text-gray-800 dark:text-white">Categorías Principales</h2>
                        <div class="flex flex-col md:flex-row gap-6 justify-center">
                            <Link :href="`/articulos?categoria=1`" class="relative group overflow-hidden rounded-lg w-full md:w-1/2 shadow-md hover:shadow-lg transition-all">
                                <img :src="clothesSection" alt="Clothes Section" class="w-full h-[400px] object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/70 to-transparent flex items-end justify-center pb-10 transition-all duration-300">
                                    <h2 class="text-white text-4xl font-bold">Ropa</h2>
                                </div>
                                <div class="absolute inset-0 bg-purple-600/10 dark:bg-purple-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </Link>
                            <Link :href="`/articulos?categoria=5`" class="relative group overflow-hidden rounded-lg w-full md:w-1/2 shadow-md hover:shadow-lg transition-all">
                                <img :src="skateSection" alt="Skate Section" class="w-full h-[400px] object-cover transition-transform duration-300 group-hover:scale-105">
                                <div class="absolute inset-0 bg-gradient-to-t from-gray-900/70 to-transparent flex items-end justify-center pb-10 transition-all duration-300">
                                    <h2 class="text-white text-4xl font-bold">Skate</h2>
                                </div>
                                <div class="absolute inset-0 bg-purple-600/10 dark:bg-purple-500/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </Link>
                        </div>
                    </div>

                    <!-- Productos destacados con estilos mejorados -->
                    <div class="bg-gray-100 dark:bg-gray-900 py-12 px-4">
                        <div class="container mx-auto">
                            <h2 class="text-3xl font-bold text-center mb-8 text-gray-800 dark:text-white">Productos Destacados</h2>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                                <div v-for="articulo in articulos" :key="articulo.id"
                                     class="bg-white dark:bg-gray-800/80 rounded-lg shadow-sm hover:shadow-md border border-gray-200 dark:border-transparent overflow-hidden transition-all hover:scale-102 duration-300" id="articulo-card">
                                    <img v-if="articulo.imagenes && articulo.imagenes.length > 0"
                                         :src="articulo.imagenes[0].ruta"
                                         :alt="articulo.nombre"
                                         class="w-full h-48 object-cover">
                                    <div v-else class="w-full h-48 bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                        <span class="text-gray-500 dark:text-gray-400">Sin imagen</span>
                                    </div>
                                    <div class="p-4">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ articulo.nombre }}</h3>
                                        <p class="text-purple-600 dark:text-purple-400 font-bold mt-1">{{ articulo.precio.toFixed(2) }} €</p>
                                        <div class="mt-4 flex justify-between">
                                            <Link :href="`/articulos/${articulo.id}`" class="text-purple-600 dark:text-purple-400 hover:text-purple-800 dark:hover:text-purple-300 hover:underline transition-colors">
                                                Ver detalles
                                            </Link>
                                            <div class="flex items-center space-x-2">
                                                <!-- Botones de administración solo visibles para administradores -->
                                                <template v-if="$page.props.auth.user && $page.props.auth.user.rol === 'admin'">
                                                    <Link :href="`/articulos/${articulo.id}/edit`"
                                                        class="flex items-center justify-center h-8 w-8 bg-blue-500 hover:bg-blue-600 dark:bg-blue-600/90 dark:hover:bg-blue-600 text-white rounded-md transition-colors shadow-sm">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                                        </svg>
                                                    </Link>
                                                    <Link :href="`/articulos/${articulo.id}`"
                                                        method="delete"
                                                        as="button"
                                                        class="flex items-center justify-center h-8 w-8 bg-red-500 hover:bg-red-600 dark:bg-red-600/90 dark:hover:bg-red-600 text-white rounded-md transition-colors shadow-sm"
                                                        onclick="return confirm('¿Estás seguro de que deseas eliminar este artículo?')">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                                        </svg>
                                                    </Link>
                                                </template>
                                                <button @click="addToCart(articulo)"
                                                    class="flex items-center justify-center h-8 px-3 bg-purple-500 hover:bg-purple-600 dark:bg-blue-500/90 dark:hover:bg-blue-500 text-white rounded-md transition-colors shadow-sm">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 mr-1">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                                    </svg>
                                                    Añadir
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Footer minimalista -->
                    <footer class="bg-white dark:bg-gray-900 py-8 border-t border-gray-200 dark:border-gray-800 mt-12">
                        <div class="container mx-auto px-4 text-center">
                            <p class="text-gray-600 dark:text-gray-400">© 2025 Caveman - Todos los derechos reservados</p>
                        </div>
                    </footer>
                </main>
        </div>
    </div>
</template>
