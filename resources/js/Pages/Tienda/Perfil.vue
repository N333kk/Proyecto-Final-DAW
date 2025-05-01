<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import logoImage from '../../../images/logo.png';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import Navbar from '@/Components/Navbar.vue';

const logout = () => {
    router.post(route('logout'));
};

defineProps({
    user: Object,
    confirmsTwoFactorAuthentication: Boolean,
    sessions: Array,

});

</script>

<template>
    <Head title="Mi Perfil" />
    <div class="bg-gray-50 text-gray-800 dark:bg-black dark:text-white min-h-screen">
        <div class="flex flex-col items-center justify-center selection:bg-purple-500 selection:text-white dark:selection:bg-[#FF2D20]">
            <Navbar />

            <main class="mt-6 w-full px-0 max-w-none">
                <div class="bg-white dark:bg-black shadow-xl text-gray-800 dark:text-white/90">
                    <div class="p-6 max-w-7xl mx-auto">
                        <h2 class="text-xl font-bold">Mi Perfil</h2>
                        <p class="mt-1 text-sm mb-6">Administra tu información y preferencias de cuenta</p>

                        <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">
                            <!-- Sección de información de perfil - Columnas 1-3 -->
                            <div class="lg:col-span-3">
                                <div v-if="$page.props.jetstream.canUpdateProfileInformation">
                                    <UpdateProfileInformationForm :user="user" />
                                    <SectionBorder />
                                </div>

                                <div v-if="$page.props.jetstream.canUpdatePassword">
                                    <UpdatePasswordForm class="mt-10 sm:mt-0" />
                                    <SectionBorder />
                                </div>

                                <template v-if="$page.props.jetstream.hasAccountDeletionFeatures">
                                    <DeleteUserForm class="mt-10 sm:mt-0" />
                                </template>
                            </div>

                            <!-- Sección para artículos favoritos - Columnas 4-5 -->
                            <div class="lg:col-span-2 bg-white dark:bg-black border border-white/15 rounded-xl p-6">
                                <h3 class="text-lg font-semibold mb-4">Mis Artículos Favoritos</h3>
                                <div
                                    class="min-h-[400px] flex items-start justify-start text-gray-500 dark:text-gray-400">
                                    <!-- Aquí se implementará la lista de artículos favoritos -->
                                    <div v-if="user.articulos_favoritos.length === 0">
                                        <p class="text-sm">Próximamente tus artículos favoritos</p>
                                    </div>
                                    <div v-else class="flex flex-row items-start justify-start flex-wrap">

                                        <!-- Aquí se implementará la lista de artículos favoritos -->
                                        <ul class="flex flex-row items-start justify-start flex-wrap">
                                            <li v-for="articulo_favorito in user.articulos_favoritos"
                                                :key="articulo_favorito.id"
                                                class="bg-white rounded-2xl shadow-md flex flex-col w-[380px] h-[100px] text-black m-4 transition-all hover:bg-white/90">
                                                <div class="flex items-center w-full p-4">
                                                    <div class="flex-shrink-0">
                                                        <img class="w-12 h-12 object-cover rounded-full"
                                                            :src="articulo_favorito.imagenes && articulo_favorito.imagenes.length > 0
                                                                ? articulo_favorito.imagenes[0].ruta
                                                                : '/img/placeholder.webp'"
                                                            alt="Imagen Articulo">
                                                    </div>
                                                    <div class="ml-4 flex-grow overflow-hidden">
                                                        <h2 class="text-base font-semibold truncate">{{ articulo_favorito.nombre }}</h2>
                                                        <div class="flex items-center justify-between mt-2">
                                                            <p class="text-base font-semibold">{{ articulo_favorito.precio }}€</p>
                                                            <div class="flex space-x-2">
                                                                <Link :href="`/articulos/${articulo_favorito.id}`"
                                                                    class="bg-gray-100 hover:bg-gray-200 text-black p-1.5 rounded-md transition-colors flex items-center justify-center">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                                                    </svg>
                                                                </Link>
                                                                <Link
                                                                    href="#"
                                                                    class="bg-red-100 hover:bg-red-200 text-red-600 p-1.5 rounded-md transition-colors flex items-center justify-center"
                                                                    @click.prevent="router.post(route('favoritos.toggle', { id: articulo_favorito.id }), {}, { preserveScroll: true })">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" />
                                                                    </svg>
                                                                </Link>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <footer class="py-16 text-center text-sm text-black dark:text-white/70">

            </footer>
        </div>
    </div>
</template>
