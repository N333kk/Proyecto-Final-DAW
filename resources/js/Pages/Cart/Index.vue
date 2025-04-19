<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
const logout = () => {
    router.post(route('logout'));
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

const precioTotal = computed(() => {
    return props.cartItems.reduce((sum, item) => sum + (item.cantidad * item.articulo.precio), 0).toFixed(2);
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


                <div v-if="cartItems.length > 0">
                    <div class="bg-white py-2 px-4 rounded-2xl shadow-md flex text-black m-4 transition-all hover:bg-white/90"
                        v-for="item in cartItems" :key="item.id">

                        <div class="flex-shrink-0 p-1 flex flex-col items-center justify-center ml-2">
                            <img class="w-12 h-12 object-cover rounded-full" :src="item.articulo.imagenes && item.articulo.imagenes.length > 0
              ? `/storage/${articulo.imagenes[0].ruta}`
              : '/img/placeholder.webp'"
                                alt="Imagen Articulo">

                        </div>

                        <div class="ml-6 transition-all">
                            <div class="flex justify-between">
                                <h2 class="text-lg font-semibold my-1">{{ item.articulo.nombre }}</h2>
                                <Link :href="`/cart/${item.articulo.id}`" method="DELETE"
                                    class="text-red-900 font-extrabold text-lg hover:text-black  hover:text-xl transition-all ml-16">
                                X</Link>
                            </div>
                            <div class="flex flex-row justify-between items-center">
                                <p class="field-sizing-content select-none appearance-none">{{ item.cantidad }}</p>
                                <div class="border">
                                    <Link :href="`/cart/${item.articulo.id}`" method="POST">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-5 stroke-white bg-black/50 border-b-white border-b hover:bg-white/50 hover:stroke-black transition-all">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                                    </svg>
                                    </Link>
                                    <Link :href="`/cart/${item.articulo.id}`" method="PUT">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor"
                                        class="size-5 stroke-white bg-black/50 hover:bg-white/50 hover:stroke-black border-t-white border-t transition-all">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                    </Link>
                                </div>
                            </div>
                            <p class="text-black font-semibold my-1">{{ item.articulo.precio }} €</p>
                        </div>



                    </div>

                    <p>Total: {{ precioTotal }} €</p>

                    <button @click="checkout"
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Proceder al pago
                    </button>

                </div>


                <p v-else>Tu carrito está vacío.</p>
            </div>
        </div>
    </div>

</template>
