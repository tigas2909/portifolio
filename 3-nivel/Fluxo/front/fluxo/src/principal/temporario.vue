<script setup>
import { logout as fazerLogoutAPI } from '../service/loginService'
import { useRouter } from 'vue-router'

const router = useRouter()

async function fazerLogout() {
    try {
        await fazerLogoutAPI()
        await router.push('/login')
    } catch (erro) {
        console.error('Erro ao fazer logout:', erro)
    }
}
</script>

<template>
    <button @click="fazerLogout">
        Logout
    </button>

    <main class="container">
        <h1>✅ OK, login feito!</h1>
        <p>Você entrou no sistema Financia.</p>
    </main>
</template>

<style scoped>
.container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    font-family: Arial, sans-serif;
    text-align: center;
}
</style>