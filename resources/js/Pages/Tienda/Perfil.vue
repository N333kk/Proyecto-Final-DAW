<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import logoImage from '../../../images/logo.png';
import DeleteUserForm from '@/Pages/Profile/Partials/DeleteUserForm.vue';
import LogoutOtherBrowserSessionsForm from '@/Pages/Profile/Partials/LogoutOtherBrowserSessionsForm.vue';
import SectionBorder from '@/Components/SectionBorder.vue';
import TwoFactorAuthenticationForm from '@/Pages/Profile/Partials/TwoFactorAuthenticationForm.vue';
import UpdatePasswordForm from '@/Pages/Profile/Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from '@/Pages/Profile/Partials/UpdateProfileInformationForm.vue';
import { Head, Link, router } from '@inertiajs/vue3'

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
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white  min-h-screen">

        <div class="flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
                <header class="min-w-full">
                    <nav class="flex min-w-screen space-x-4 sm:justify-between justify-center p-6 bg-black text-white dark:text-white/80 border-b border-white/20">
                            <div class="space-x-4">
                                <div>
                                    <img :src="logoImage" style="height:96px;" alt="Logo">
                                </div>
                            <Link href="/tienda" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">Inicio</Link>
                            <Link href="/articulos" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">Articulos</Link>
                            <Link href="/pedidos" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">Pedidos</Link>
                            <Link href="/perfil" class="text-sm font-medium  hover:text-black dark:hover:text-white/50">{{ $page.props.auth.user.name }}</Link>
                            <Link v-if="$page.props.auth.user.rol == 'admin'" href="/dashboard"
                            class="text-sm font-medium  hover:text-black dark:hover:text-white/50">Dashboard</Link>
                        </div>
                        <div class="flex">
                            <form @submit.prevent="logout">
                                            <button type="submit" class="text-sm font-medium px-4 text-white dark:text-white/80 hover:text-white/50 dark:hover:text-white/50">
                                                Log Out
                                            </button>
                                        </form>
                        <Link href="/cart">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6 dark:stroke-white dark:hover:stroke-white/50">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                        </Link>
                    </div>
                    </nav>
                </header>

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
                                    <div class="min-h-[400px] flex items-center justify-center text-gray-500 dark:text-gray-400">
                                        <!-- Aquí se implementará la lista de artículos favoritos -->
                                        <p class="text-sm">Próximamente tus artículos favoritos</p>
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
