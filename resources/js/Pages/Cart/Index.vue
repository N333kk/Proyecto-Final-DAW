<script setup>
import { computed, ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
const logout = () => {
    router.post(route('logout'));
};

// Función para determinar si la URL es completa (comienza con http:// o https://)
const isFullUrl = (url) => {
    return url && (url.startsWith('http://') || url.startsWith('https://'));
};

const checkout = async () => {
    console.log('Iniciando checkout con axios...');
    try {
        // Petición POST con axios
        const response = await axios.post('/cart/checkout');

        console.log('Respuesta del servidor:', response);

        if (response.data && response.data.url) {
            console.log('URL recibida:', response.data.url);
            // Redirige el navegador a la URL de Stripe
            window.location.href = response.data.url;
        } else {
            console.error('No se recibió una URL válida en la respuesta:', response.data);
            alert('Error: No se pudo obtener la URL de pago.');
        }
    } catch (error) {
        console.error('Error durante la petición de checkout:', error);
        if (error.response) {
            console.error('Datos del error:', error.response.data);
            console.error('Estado del error:', error.response.status);
            alert(`Error ${error.response.status}: ${error.response.data.error || 'Ocurrió un error en el servidor.'}`);
        } else if (error.request) {
            console.error('No se recibió respuesta del servidor:', error.request);
            alert('Error: No se pudo conectar con el servidor.');
        } else {
            console.error('Error al configurar la petición:', error.message);
            alert('Error: Ocurrió un error inesperado.');
        }
    }
};

const props = defineProps({
    cartItems: Array
});

// Crear una copia local reactiva de los elementos del carrito
const localCartItems = ref([...props.cartItems]);

// Función para incrementar cantidad (actualización optimista)
const incrementarCantidad = (item) => {
    // 1. Buscar el ítem en el carrito local
    const itemIndex = localCartItems.value.findIndex(i => i.articulo.id === item.articulo.id);
    if (itemIndex !== -1) {
        // 2. Actualizar inmediatamente la UI
        localCartItems.value[itemIndex].cantidad += 1;

        // 3. Enviar la petición al servidor en segundo plano
        router.post(`/cart/${item.articulo.id}`, {}, {
            preserveScroll: true,
            preserveState: true,
            onError: (errors) => {
                // Si hay error, revertir el cambio en la UI
                localCartItems.value[itemIndex].cantidad -= 1;
                console.error('Error al incrementar cantidad:', errors);
            }
        });
    }
};

// Función para decrementar cantidad (actualización optimista)
const decrementarCantidad = (item) => {
    // 1. Buscar el ítem en el carrito local
    const itemIndex = localCartItems.value.findIndex(i => i.articulo.id === item.articulo.id);
    if (itemIndex !== -1 && localCartItems.value[itemIndex].cantidad > 1) {
        // 2. Actualizar inmediatamente la UI
        localCartItems.value[itemIndex].cantidad -= 1;

        // 3. Enviar la petición al servidor en segundo plano
        router.put(`/cart/${item.articulo.id}`, {}, {
            preserveScroll: true,
            preserveState: true,
            onError: (errors) => {
                // Si hay error, revertir el cambio en la UI
                localCartItems.value[itemIndex].cantidad += 1;
                console.error('Error al decrementar cantidad:', errors);
            }
        });
    }
};

// Eliminar artículo del carrito (actualización optimista)
const eliminarDelCarrito = (item) => {
    // 1. Guardar el estado anterior para restaurar en caso de error
    const itemsAntes = [...localCartItems.value];

    // 2. Actualizar inmediatamente la UI
    localCartItems.value = localCartItems.value.filter(i => i.articulo.id !== item.articulo.id);

    // 3. Enviar la petición al servidor en segundo plano
    router.delete(`/cart/${item.articulo.id}`, {
        preserveScroll: true,
        onError: (errors) => {
            // Si hay error, revertir el cambio en la UI
            localCartItems.value = itemsAntes;
            console.error('Error al eliminar artículo:', errors);
        }
    });
};

// Recalcular el precio total basado en el carrito local
const precioTotal = computed(() => {
    return localCartItems.value.reduce((sum, item) => sum + (item.cantidad * item.articulo.precio), 0).toFixed(2);
});

</script>

<template>
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white  min-h-screen">
        <div class="flex flex-col items-start justify-center selection:bg-[#FF2D20] selection:text-white ">
            <header class="min-w-full">
                <nav
                    class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-black text-white dark:text-white/80 border-b border-white/20">
                    <div class="space-x-4">
                        <Link href="/tienda" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">
                        Inicio</Link>
                        <Link href="/articulos" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">
                        Articulos</Link>
                        <Link href="/pedidos" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">
                        Pedidos</Link>
                        <Link href="/perfil" class="text-sm font-medium hover:text-black dark:hover:text-white/50">{{
                            $page.props.auth.user.name }}</Link>
                    </div>
                    <div class="flex">
                        <form @submit.prevent="logout">
                            <button type="submit"
                                class="text-sm font-medium px-4 text-white dark:text-white/80 hover:text-white/50 dark:hover:text-white/50">
                                Log Out
                            </button>
                        </form>
                        <Link href="/cart">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6 dark:stroke-white dark:hover:stroke-white/50">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>
                        </Link>
                    </div>
                </nav>
            </header>
            <div class="px-8 flex flex-row min-w-full border-b border-white/20">
                <h2 class="pt-6 pb-6 font-extrabold text-4xl">Tu Carrito</h2>
            </div>
            <div class="flex flex-col items-center px-16 py-4 border-r border-white/20 min-h-screen">


                <div v-if="localCartItems.length > 0">
                    <div class="bg-white py-2 px-8 rounded-2xl shadow-md flex text-black m-4 transition-all hover:bg-white/90"
                        v-for="item in localCartItems" :key="item.id">

                        <div class="flex-shrink-0 flex flex-col items-center justify-center ml-2">
                            <img class="w-12 h-12 object-cover rounded-full"
                                :src="item.articulo.imagenes && item.articulo.imagenes.length > 0
                                    ? (isFullUrl(item.articulo.imagenes[0].ruta)
                                        ? item.articulo.imagenes[0].ruta
                                        : `/storage/${item.articulo.imagenes[0].ruta}`)
                                    : '/img/placeholder.webp'"
                                alt="Imagen Articulo">
                        </div>

                        <div class="ml-6 transition-all flex-grow">
                            <div class="flex justify-between">
                                <h2 class="text-lg font-semibold my-1">{{ item.articulo.nombre }}</h2>
                                <button @click="eliminarDelCarrito(item)"
                                    class="text-red-900 font-extrabold text-lg hover:text-black hover:text-xl transition-all ml-4">
                                    X
                                </button>
                            </div>

                            <div class="flex items-center gap-2">
                                <p class="text-base font-medium">{{ item.cantidad }}</p>

                                <div class="flex flex-col border border-gray-300 rounded overflow-hidden">
                                    <button @click="incrementarCantidad(item)" type="button"
                                        class="flex items-center justify-center h-6 w-6 bg-black/50 hover:bg-white/50 transition-all">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="w-4 h-4 stroke-white hover:stroke-black">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                        </svg>
                                    </button>

                                    <button @click="decrementarCantidad(item)" type="button"
                                        class="flex items-center justify-center h-6 w-6 bg-black/50 hover:bg-white/50 transition-all border-t border-white/20">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="1.5" stroke="currentColor"
                                            class="w-4 h-4 stroke-white hover:stroke-black">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </div>

                                <p class="text-black font-semibold ml-auto">{{ item.articulo.precio }} €</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sección de total y checkout con mejores estilos -->
                    <div class="mt-6 p-4 bg-white/10 rounded-xl border border-white/20">
                        <p class="text-xl font-bold mb-4">Total: {{ precioTotal }} €</p>

                        <button @click="checkout"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition-colors">
                            Proceder al pago
                        </button>
                    </div>
                </div>

                <p v-else class="text-center text-lg mt-8">Tu carrito está vacío.</p>
            </div>
        </div>
    </div>

</template>
